<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrUpdateBranchRequest;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Branch::class);

        $branches = Branch::latest()
            ->get()
            ->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'address' => $branch->address,
                    'created_at' => $branch->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return Inertia::render('Branches/Index', [
            'branches' => $branches,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrUpdateBranchRequest $request): RedirectResponse
    {
        $data = $request->validated();
        Branch::create($data);

        return redirect()->back();

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateOrUpdateBranchRequest $request, Branch $branch): RedirectResponse
    {
        $data = $request->validated();
        
        $branch->update($data);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Branch $branch): RedirectResponse
    {
        $this->authorize('delete', $branch);

        $branch->delete();

        return redirect()->back();
    }
}
