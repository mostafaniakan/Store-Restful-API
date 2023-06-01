<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class BrandController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $brands=Brand::paginate(2);

        return  $this->successResponse('',[
            'brands'=>BrandResource::collection($brands),
            'links'=>BrandResource::collection($brands)->response()->getData()->links,
            'meta'=>BrandResource::collection($brands)->response()->getData()->meta,
        ],200);
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
            'name' => 'required|unique:brands',
            'display_name' => 'required',
        ]);
        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 402);
        }

        DB::beginTransaction();
        $brand = Brand::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
        ]);
        DB::commit();
        return $this->successResponse('ok', new BrandResource($brand), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $brand=Brand::find($id);
        return $this->successResponse('',new BrandResource($brand),200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, Brand $brand)
    {

        $validation = Validator::make($request->all(), [
            'name' => 'required|unique:brands',
            'display_name' => 'required',
        ]);
        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 402);
        }

        DB::beginTransaction();
        $brand->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
        ]);
        DB::commit();
        return $this->successResponse('ok', new BrandResource($brand), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Brand $brand)
    {

        DB::beginTransaction();
        $brand->delete();
        DB::commit();
        return $this->successResponse('ok', new BrandResource($brand), 200);
    }
    public function products(Brand $brand)
    {

        return $this->successResponse('',new BrandResource($brand->load('products')),200);
    }
}
