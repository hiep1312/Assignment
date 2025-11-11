<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $repository
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->repository->getAll(criteria: function(&$query) use ($request) {
            $query->when($request->search, function($innerQuery) use ($request){
                $innerQuery->where(function($subQuery) use ($request){
                    $subQuery->whereLike('title', '%'. trim($request->search) .'%')
                        ->orWhereLike('description', '%'. trim($request->search) .'%');
                });
            })->when(
                $request->status !== null,
                fn($innerQuery) => $innerQuery->where('status', $this->status)
            )->when(
                $request->category !== null,
                fn($innerQuery) => $innerQuery->whereHas('categories', function($subQuery) {
                    $subQuery->where('categories.slug', $request->category);
                })
            );

        }, perPage: 20, columns: ['title', 'slug', 'description', 'status', 'created_at'], pageName: 'page');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $validatedData = $request->validated();

        return $this->repository->create($validatedData);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        return $this->repository->first(criteria: function($query) use ($slug) {
            $query->where('slug', $slug);
        }, columns: ['title', 'slug', 'description', 'status', 'created_at'], throwNotFound: false);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $validatedData = $request->validated();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
