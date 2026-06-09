<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private function superAdminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);
    }

    private function normalAdminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => false,
        ]);
    }

    /** @test */
    public function guest_is_redirected_from_super_admin_routes(): void
    {
        $this->get(route('super_admin.index'))
            ->assertRedirect('/');
    }

    /** @test */
    public function non_super_admin_is_redirected_from_super_admin_routes(): void
    {
        $admin = $this->normalAdminUser();

        $this->actingAs($admin)
            ->get(route('super_admin.index'))
            ->assertRedirect('/');
    }

    /** @test */
    public function super_admin_can_view_admins_index_and_only_sees_non_super_admin_admins(): void
    {
        $superAdmin = $this->superAdminUser();
        $listedAdmin = $this->normalAdminUser();
        $otherSuperAdmin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);
        $nonAdmin = User::factory()->create([
            'is_admin' => false,
            'is_super_admin' => false,
        ]);

        $this->actingAs($superAdmin)
            ->get(route('super_admin.index'))
            ->assertOk()
            ->assertViewHas('admins', function ($admins) use ($listedAdmin, $otherSuperAdmin, $nonAdmin) {
                return $admins->contains('id', $listedAdmin->id)
                    && ! $admins->contains('id', $otherSuperAdmin->id)
                    && ! $admins->contains('id', $nonAdmin->id);
            });
    }

    /** @test */
    public function super_admin_can_create_admin_user(): void
    {
        $superAdmin = $this->superAdminUser();

        $this->actingAs($superAdmin)
            ->post(route('super_admin.store'), [
                'name' => 'Nuevo Admin',
                'email' => 'nuevo-admin@example.com',
                'phone_number' => '123456789',
                'password' => 'StrongPass1',
                'password_confirmation' => 'StrongPass1',
            ])
            ->assertRedirect(route('super_admin.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Nuevo Admin',
            'email' => 'nuevo-admin@example.com',
            'phone_number' => '123456789',
            'is_admin' => true,
            'is_super_admin' => false,
        ]);

        $createdAdmin = User::where('email', 'nuevo-admin@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('StrongPass1', $createdAdmin->password));
    }

    /** @test */
    public function super_admin_cannot_create_admin_with_invalid_data(): void
    {
        $superAdmin = $this->superAdminUser();
        User::factory()->create(['email' => 'duplicate@example.com']);

        $this->actingAs($superAdmin)
            ->from(route('super_admin.create'))
            ->post(route('super_admin.store'), [
                'name' => '',
                'email' => 'duplicate@example.com',
                'phone_number' => str_repeat('1', 30),
                'password' => 'weak',
                'password_confirmation' => 'different',
            ])
            ->assertRedirect(route('super_admin.create'))
            ->assertSessionHasErrors(['name', 'email', 'phone_number', 'password']);
    }

    /** @test */
    public function super_admin_can_update_admin_without_changing_password(): void
    {
        $superAdmin = $this->superAdminUser();
        $admin = $this->normalAdminUser();
        $originalPassword = $admin->password;

        $this->actingAs($superAdmin)
            ->put(route('super_admin.update', $admin), [
                'name' => 'Admin Editado',
                'email' => 'admin-editado@example.com',
                'phone_number' => '555444333',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('super_admin.index'))
            ->assertSessionHas('success');

        $admin->refresh();

        $this->assertSame('Admin Editado', $admin->name);
        $this->assertSame('admin-editado@example.com', $admin->email);
        $this->assertSame('555444333', $admin->phone_number);
        $this->assertSame($originalPassword, $admin->password);
    }

    /** @test */
    public function super_admin_can_update_admin_password(): void
    {
        $superAdmin = $this->superAdminUser();
        $admin = $this->normalAdminUser();

        $this->actingAs($superAdmin)
            ->put(route('super_admin.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'phone_number' => $admin->phone_number,
                'password' => 'NewStrongPass1',
                'password_confirmation' => 'NewStrongPass1',
            ])
            ->assertRedirect(route('super_admin.index'));

        $admin->refresh();

        $this->assertTrue(Hash::check('NewStrongPass1', $admin->password));
    }

    /** @test */
    public function super_admin_cannot_edit_another_super_admin(): void
    {
        $superAdmin = $this->superAdminUser();
        $targetSuperAdmin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        $this->actingAs($superAdmin)
            ->get(route('super_admin.edit', $targetSuperAdmin))
            ->assertForbidden();
    }

    /** @test */
    public function super_admin_can_toggle_admin_access_for_non_super_admin_user(): void
    {
        $superAdmin = $this->superAdminUser();
        $admin = $this->normalAdminUser();

        $this->actingAs($superAdmin)
            ->post(route('super_admin.toggle', $admin))
            ->assertRedirect(route('super_admin.index'))
            ->assertSessionHas('success');

        $this->assertFalse($admin->fresh()->is_admin);
    }

    /** @test */
    public function super_admin_cannot_toggle_super_admin_access(): void
    {
        $superAdmin = $this->superAdminUser();
        $targetSuperAdmin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        $this->actingAs($superAdmin)
            ->post(route('super_admin.toggle', $targetSuperAdmin))
            ->assertForbidden();
    }

    /** @test */
    public function super_admin_can_delete_non_super_admin_user(): void
    {
        $superAdmin = $this->superAdminUser();
        $admin = $this->normalAdminUser();

        $this->actingAs($superAdmin)
            ->delete(route('super_admin.destroy', $admin))
            ->assertRedirect(route('super_admin.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }

    /** @test */
    public function super_admin_cannot_delete_super_admin_user(): void
    {
        $superAdmin = $this->superAdminUser();
        $targetSuperAdmin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        $this->actingAs($superAdmin)
            ->delete(route('super_admin.destroy', $targetSuperAdmin))
            ->assertForbidden();
    }
}