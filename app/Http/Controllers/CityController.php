<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $city=City::paginate(5);

        return $this->successResponse('',[
            'status'=>CityResource::collection($city),
            'links'=>CityResource::collection($city)->response()->getData()->links,
            'meta'=>CityResource::collection($city)->response()->getData()->meta,
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'province_id' =>'required',
        ]);

        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 422);
        }

        $city=City::create([
            'name'=>$request->name,
            'province_id'=>$request->province_id
        ]);

        return $this->successResponse('ok',new CityResource($city),200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $city=City::find($id);
        return $this->successResponse('',new CityResource($city),200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, City $city)
    {

        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'province_id' =>'required',
        ]);

        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 422);
        }

        $city->update([
            'name'=>$request->name,
            'province_id'=>$request->province_id
        ]);

        return $this->successResponse('ok',new CityResource($city),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(City $city)
    {
        $city->delete();
        return $this->successResponse('deleted',new CityResource($city),200);
    }
}
