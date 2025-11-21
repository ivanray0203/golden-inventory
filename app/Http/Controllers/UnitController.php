<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrUpdateUnitRequest;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Unit::class);

        $units = Unit::latest()
            ->get()
            ->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'description' => $unit->description,
                    'created_at' => $unit->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return Inertia::render('Units/Index', [
            'units' => $units,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrUpdateUnitRequest $request): RedirectResponse
    {
        $data = $request->validated();
        Unit::create($data);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateOrUpdateUnitRequest $request, Unit $unit): RedirectResponse
    {
        $data = $request->validated();

        $unit->update($data);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Unit $unit): RedirectResponse
    {
        $this->authorize('delete', $unit);

        $unit->delete();

        return redirect()->back();
    }
}
