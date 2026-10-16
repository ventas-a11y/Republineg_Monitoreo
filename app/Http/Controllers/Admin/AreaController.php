<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AreaRequest;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Área management for Administradores. Authorization lives on the routes
 * (AreaPolicy through the `can` middleware).
 */
class AreaController extends Controller
{
    /**
     * List the Áreas with how many Empleados and Revisores each has.
     */
    public function index(): Response
    {
        $areas = Area::query()
            ->withCount(['employees', 'reviewers'])
            ->orderBy('name')
            ->get()
            ->map(fn (Area $area): array => [
                'id' => $area->id,
                'name' => $area->name,
                'employees_count' => $area->employees_count,
                'reviewers_count' => $area->reviewers_count,
            ]);

        return Inertia::render('admin/areas/index', [
            'areas' => $areas,
        ]);
    }

    /**
     * Create an Área.
     */
    public function store(AreaRequest $request): RedirectResponse
    {
        Area::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Área creada.']);

        return redirect()->route('admin.areas.index');
    }

    /**
     * Show the form to rename an Área.
     */
    public function edit(Area $area): Response
    {
        return Inertia::render('admin/areas/edit', [
            'area' => ['id' => $area->id, 'name' => $area->name],
        ]);
    }

    /**
     * Rename an Área.
     */
    public function update(AreaRequest $request, Area $area): RedirectResponse
    {
        $area->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Área actualizada.']);

        return redirect()->route('admin.areas.index');
    }
}
