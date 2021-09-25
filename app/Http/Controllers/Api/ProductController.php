<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\ProductPicture;
use App\Models\ProductPrice;
use App\Models\Like;
use App\Http\Resources\ProductResource;
use Symfony\Component\HttpFoundation\Response;

use App\Http\Requests\Product\ProductImageRequest;
use App\Http\Requests\Product\ProductPriceRequest;
use App\Http\Requests\Product\ProductIndexRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductDestroyRequest;

class ProductController extends Controller
{
    public $page_size = 10;

    public function index(ProductIndexRequest $request) 
    {
        $queryset = Product::baseQuery();

        if ($request->has('sort_by_price')) 
                $queryset = $queryset->orderBy('selling_price', $request->sort_by_price);
        if ($request->has('user_id'))
                $queryset = $queryset->where('user_id', $request->user_id);
        if ($request->has('low_limit_price'))
                $queryset = $queryset->lowLimit($request->low_limit_price);
        if ($request->has('high_limit_price'))
            $queryset = $queryset->highLimit($request->high_limit_price);
        if ($request->has('search'))
                $queryset = $queryset->where('description', 'like', '%'.$request->search.'%');

        $queryset = $queryset->orderBy('created_at', 'desc');
        $queryset = $queryset->paginate($this->page_size);
        return ProductResource::collection($queryset);
    }
    public function show(ProductIndexRequest $request, Product $product) 
    {
        return new ProductResource($product);
    }
    public function store(ProductStoreRequest $request) 
    {
        $validated_data = $request->validated();
        $validated_data['user_id'] = auth()->user()->id;

        $pictures = $validated_data['pictures'];
        unset($validated_data['pictures']);
        $price = $validated_data['price'];
        unset($validated_data['price']);

        $new_product = Product::create($validated_data);
        ProductPrice::create($price + ['product_id'=>$new_product->id]);
        foreach ($pictures as $picture) {
            ProductPicture::save_and_create(
                $picture['file'], $new_product->id,
            );
        }

        return new ProductResource($new_product);
    }
    
    public function update(ProductUpdateRequest $request, Product $product) 
    { # this function only updates the post not it files
        $validated_data = $request->validated();

        $product->update($validated_data);
        return new ProductResource($product);
    }
    public function destroy(ProductDestroyRequest $request, Product $product) 
    {
        $product->delete();
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
    
    public function add_picture(ProductImageRequest $request, Product $product) 
    {
        $file = $request->validated()['file'];

        return response()->json([
            'data'=> ProductPicture::save_and_create(
                $file, $product->id,
            )
        ], Response::HTTP_CREATED);
    }
    public function delete_picture(ProductImageRequest $request, Product $product, ProductPicture $picture)
    {
        $picture->delete();
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
    public function update_price(ProductPriceRequest $request, Product $product) 
    {
        $validated_data = $request->validated();

        return response()->json([
            'data' => ProductPrice::create($validated_data+['product_id'=>$product->id]),
        ], Response::HTTP_CREATED);
    }
    public function toggle_like(Request $request, Product $product) {
        $user_id = auth()->user()->id;
        $liked = $product->likes()->where(['user_id' => $user_id])->first();
        if ($liked !== null) {
            $liked->delete();
            return response()->json([], Response::HTTP_NO_CONTENT);
        } else {
            $product->likes()->save(
                New Like(['user_id' => $user_id])
            );
            return response()->json([], Response::HTTP_CREATED);
        }
    }
}
