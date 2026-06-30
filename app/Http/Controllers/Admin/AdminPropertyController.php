<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AdminPropertyController extends Controller
{
    /**
     * Display properties owned by the authenticated guest.
     */
    public function properties(): View
    {
        $scope = request()->query('scope', 'mine');
        $user = Auth::user();

        $properties = ($user->isSuperAdmin() && $scope === 'all')
            ? Property::with('owner')->get()
            : Property::where('owner_id', Auth::id())->get();

        return view('admin.admin', compact('properties', 'scope'));
    }
}
