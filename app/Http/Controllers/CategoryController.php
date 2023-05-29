<?php

namespace App\Http\Controllers;


use App\Http\Resources\CategoryResource;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $categories = Category::paginate(5);
        return $this->successResponse('0k', [
            'categories' => CategoryResource::collection($categories),
            'links' => CategoryResource::collection($categories)->response()->getData()->links,
            'meta' => CategoryResource::collection($categories)->response()->getData()->meta,
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => "required|string",
            'parent_id' => 'required|integer',
        ]);
        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 402);
        }

        DB::beginTransaction();
        $Category = Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);
        DB::commit();
        return $this->successResponse('ok', new CategoryResource($Category), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category)
    {
        return $this->successResponse('', new CategoryResource($category), 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, Category $category)
    {

        $validation = Validator::make($request->all(), [
            'parent_id' => 'required|integer',
            'name' => "required|string",

        ]);
        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 402);
        }

        DB::beginTransaction();
        $category->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);
        DB::commit();
        return $this->successResponse('ok', new CategoryResource($category), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Category $category)
    {
        DB::beginTransaction();
        $category->delete();
        DB::commit();
        return $this->successResponse('ok', new CategoryResource($category), 200);
    }

    public function children(Category $category)
    {
        return $this->successResponse('',new CategoryResource($category->load('children')),200);
    }
    public function parent(Category $category)
    {
        return $this->successResponse('',new CategoryResource($category->load('parent')),200);
    }
    public function products(Category $category)
    {
        return $this->successResponse('',new CategoryResource($category->load('products')),200);
    }
}
