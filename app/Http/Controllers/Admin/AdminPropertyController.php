<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AdminPropertyController extends Controller
{
    /**
     * Display properties owned by the authenticated guest.
     */
    public function properties(): View|RedirectResponse
    {
        $scope = request()->query('scope', 'mine');
        $user = Auth::user();

        $properties = ($user->isSuperAdmin() && $scope === 'all')
            ? Property::with('owner')->paginate(20)
            : Property::where('owner_id', Auth::id())->paginate(20);

        if ($properties->currentPage() > $properties->lastPage() && $properties->lastPage() > 0) {
            return redirect()->route('admin.properties', array_merge(request()->query(), ['page' => $properties->lastPage()]));
        }

        $properties->withQueryString();

        return view('admin.admin', compact('properties', 'scope'));
    }
}
