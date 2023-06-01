<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ProductController extends ApiController
{


    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $products = Product::paginate(5);
        return $this->successResponse('', [
            'product' => ProductResource::collection($products->load('images')),
            'links' => ProductResource::collection($products)->response()->getData()->links,
            'meta' => ProductResource::collection($products)->response()->getData()->meta,

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
            'brand_id' => 'required|integer',
            'category_id' => 'required|integer',
            'primary_image' => 'required|image',
            'price' => 'required|integer',
            'quantity' => 'integer',
            'delivery_amount' => 'integer|nullable',
            'description' => 'required',
            'images.*' => 'image|nullable'
        ]);
        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 402);
        }
        DB::beginTransaction();

        $primaryImageName = Carbon::now()->microsecond . '.' . $request->primary_image->extension();
        $request->primary_image->storeAs('images/product', $primaryImageName, 'public');
        if ($request->has('images')) {
            $fileNameImages = [];
            foreach ($request->images as $image) {
                $fileNameImage = Carbon::now()->microsecond . '.' . $image->extension();
                $image->storeAs('images/product', $fileNameImage, 'public');
                $fileNameImages[] = $fileNameImage;
            }
        }


        $product = Product::create([
            'name' => $request->name,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'primary_image' => $primaryImageName,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'delivery_amount' => $request->delivery_amount,
            'description' => $request->description,
            'images.*' => $request->images
        ]);

        if ($request->has('images')) {
            foreach ($fileNameImages as $fileNameImage) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $fileNameImage,
                ]);
            }
        }
        DB::commit();
        return $this->successResponse(new ProductResource($product), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $product=Product::find($id);
        return $this->successResponse(new ProductResource($product->load('images')), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, Product $product)
    {
        $validation = Validator::make($request->all(), [
            'name' => "string",
            'brand_id' => 'integer',
            'category_id' => 'integer',
            'primary_image' => 'image',
            'price' => 'integer',
            'quantity' => 'integer',
            'delivery_amount' => 'integer|nullable',
            'description' => 'string',
            'images.*' => 'image|nullable'
        ]);
        if ($validation->fails()) {
            return $this->errorResponse($validation->messages(), 402);
        }

//        If there is an error in saving information show error and rollback
        DB::beginTransaction();

//        set image
        if ($request->has('primary_image')) {
            $primaryImageName = Carbon::now()->microsecond . '.' . $request->primary_image->extension();
            $request->primary_image->storeAs('images/product', $primaryImageName, 'public');
        }

//        set image children
        if ($request->has('images')) {
            $fileNameImages = [];
            foreach ($request->images as $image) {
                $fileNameImage = Carbon::now()->microsecond . '.' . $image->extension();
                $image->storeAs('images/product', $fileNameImage, 'public');
                $fileNameImages[] = $fileNameImage;
            }
        }


        $product->update([
            'name' => $request->has('name') ? $request->name : $product->name,
            'brand_id' => $request->has('brand_id') ? $request->brand_id : $product->brand_id,
            'category_id' => $request->has('category_id') ? $request->category_id : $product->category_id,
            'primary_image' => $request->has('primary_image') ? $primaryImageName : $product->primary_image,
            'price' => $request->has('price') ? $request->price : $product->price,
            'quantity' => $request->has('quantity') ? $request->quantity : $product->quantity,
            'delivery_amount' => $request->has('delivery_amount') ? $request->delivery_amount : $product->delivery_amount,
            'description' => $request->has('description') ? $request->description : $product->description,
            'images.*' => $request->has('images') ? $request->images : $product->images,
        ]);

//        if has images create image children
        if ($request->has('images')) {

            // images relation name
            foreach ($product->images as $prodoctImage) {
                $prodoctImage->delete();
            }
            foreach ($fileNameImages as $fileNameImage) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $fileNameImage,
                ]);
            }
        }
        DB::commit();
        return $this->successResponse(new ProductResource($product), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Product $product)
    {

        DB:: beginTransaction();
        $product->delete();
        $images = $product->load('images');
        if ($images->has('images')) {
            foreach ($images->images as $image) {
                $image->delete();
            }
        }
        DB::commit();
        return $this->successResponse(new ProductResource($product), 200);

    }
}
