<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrUpdateProductClassRequest;
use App\Models\ProductClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', ProductClass::class);

        $productClasses = ProductClass::latest()
            ->get()
            ->map(function ($productClass) {
                return [
                    'id' => $productClass->id,
                    'name' => $productClass->name,
                    'description' => $productClass->description,
                    'created_at' => $productClass->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return Inertia::render('ProductClasses/Index', [
            'productClasses' => $productClasses,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrUpdateProductClassRequest $request): RedirectResponse
    {
        $data = $request->validated();
        ProductClass::create($data);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateOrUpdateProductClassRequest $request, ProductClass $productClass): RedirectResponse
    {
        $data = $request->validated();

        $productClass->update($data);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ProductClass $productClass): RedirectResponse
    {
        $this->authorize('delete', $productClass);

        $productClass->delete();

        return redirect()->back();
    }
}
