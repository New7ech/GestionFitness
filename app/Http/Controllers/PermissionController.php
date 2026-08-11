<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    public function index(Request $request): View
    {
        $permissions = Permission::query()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('permissions.index', compact('permissions'));
    }

    public function create(): View
    {
        return view('permissions.create');
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Permission::query()->create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $this->forgetPermissionCache();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission creee avec succes.');
    }

    public function edit(Permission $permission): View
    {
        return view('permissions.edit', compact('permission'));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validated();

        $permission->update([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $this->forgetPermissionCache();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission mise a jour avec succes.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        if ($permission->roles()->count() > 0) {
            return redirect()
                ->route('permissions.index')
                ->with('error', 'Impossible de supprimer cette permission car elle est assignee a des roles.');
        }

        $permission->delete();
        $this->forgetPermissionCache();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission supprimee avec succes.');
    }

    private function forgetPermissionCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
