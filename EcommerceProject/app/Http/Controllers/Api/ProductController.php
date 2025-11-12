<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ProductRequest;
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
        $products = $this->repository->getAll(
            criteria: function(&$query) use ($request) {
                $query->when(isset($request->search), function($innerQuery) use ($request){
                    $innerQuery->where(function($subQuery) use ($request){
                        $subQuery->whereLike('title', '%'. trim($request->search) .'%')
                            ->orWhereLike('description', '%'. trim($request->search) .'%');
                    });
                })->when(
                    isset($request->status),
                    fn($innerQuery) => $innerQuery->where('status', $request->status)
                )->when(
                    isset($request->category),
                    fn($innerQuery) => $innerQuery->whereHas('categories', function($subQuery) use ($request){
                        $subQuery->where('categories.slug', $request->category);
                    })
                );

            },
            perPage: min($request->integer('per_page', 20), 50),
            columns: ['title', 'slug', 'description', 'status', 'created_at'],
            pageName: 'page'
        );

        return response()->json([
            'success' => true,
            'message' => 'Product list retrieved successfully.',
            ...$products->toArray()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $validatedData = $request->validated();
        $createdProduct = $this->repository->create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $createdProduct,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $product = $this->repository->first(
            criteria: function($query) use ($slug) {
                $query->where('slug', $slug);
            },
            columns: ['title', 'slug', 'description', 'status', 'created_at'],
            throwNotFound: false
        );

        return response()->json([
            'success' => (bool) $product,
            'message' => $product
                ? 'Product retrieved successfully.'
                : 'Product not found.',
            'data' => $product,
        ], $product ? 200 : 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $slug)
    {
        $validatedData = $request->validated();
        $isUpdated = $this->repository->update(
            idOrCriteria: $request->id,
            attributes: $validatedData,
            updatedModel: $updatedProduct
        );

        return response()->json([
            'success' => (bool) $isUpdated,
            'message' => $isUpdated
                ? 'Product updated successfully.'
                : 'Product not found.',
            'data' => $updatedProduct,
        ], $isUpdated ? 200 : 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $isDeleted = $this->repository->delete(
            idOrCriteria: fn($query) => $query->where('slug', $slug)
        );

        return response()->json([
            'success' => (bool) $isDeleted,
            'message' => $isDeleted
                ? 'Product deleted successfully.'
                : 'Product not found.',
        ], $isDeleted ? 200 : 404);
    }
}
