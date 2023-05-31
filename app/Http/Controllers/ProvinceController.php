<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProvinceResource;
use App\Models\Province;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProvinceController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
      $province=Province::paginate(5);

      return $this->successResponse('',[
          'status'=>ProvinceResource::collection($province),
          'links'=>ProvinceResource::collection($province)->response()->getData()->links,
          'meta'=>ProvinceResource::collection($province)->response()->getData()->meta,
      ],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);
        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 422);
        }
        $province = Province::create([
            'name' => $request->name]);
        return $this->successResponse('ok', new ProvinceResource($province), 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(Province $province)
    {
        return $this->successResponse('',new ProvinceResource($province),200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, Province $province)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);
        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 422);
        }
        $province->update([
            'name' => $request->name]);
        return $this->successResponse('ok', new ProvinceResource($province), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(Province $province)
    {
        $province->delete();
      return  $this->successResponse('deleted',$province,200);
    }

    public function cities(Province $province){
        return $this->successResponse('',new ProvinceResource($province->load('cities')));
    }
}
