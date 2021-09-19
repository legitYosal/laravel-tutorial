<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Product;
use App\Models\ProductPicture;
use App\Models\ProductPrice;
use App\Http\Resources\ProductResource;

use App\Http\Requests\Product\ProductRequest;
use App\Http\Requests\Product\ProductImageRequest;
use App\Http\Requests\Product\ProductPriceRequest;

class ProductController extends Controller
{
    //
    public function index(ProductRequest $request) 
    {
        $filtering_params = [];
        if ($request->has('user_id')) {
            $filtering_params = $filtering_params + [
                'user_id' => $request->user_id
            ];
        }
        if ($request->has('sort_by_price'))
            $filtering_params = $filtering_params + [
                'sort_by_price' => $request->sort_by_price
            ];
        if ($request->has('low_limit_price'))
            $filtering_params = $filtering_params + [
                'low_limit_price' => $request->low_limit_price
            ];
        if ($request->has('high_limit_price'))
            $filtering_params = $filtering_params + [
                'high_limit_price' => $request->high_limit_price
            ];
        if ($request->has('search'))
            $filtering_params = $filtering_params + [
                'search' => $request->search
            ];

        Validator::make($filtering_params, [
            'user_id' => ['sometimes', 'exists:users,id'],
            'sort_by_price' => ['sometimes', 'in:desc,asc'],
            'low_limit_price' => ['sometimes', 'integer'],
            'high_limit_price' => ['sometimes', 'integer'],
            'search' => ['sometimes', 'string', ]
        ])->validate();

        $last_selling_price_subquery = '(select product_prices.selling_price from product_prices
        where product_prices.product_id = products.id 
        order by created_at desc limit 1)';

        $queryset = Product::with('prices')->with('images')
                        ->selectRaw(
                            '*, '.$last_selling_price_subquery.' as selling_price'
                        );


        if ($request->has('sort_by_price')) 
                $queryset = $queryset->orderBy('selling_price', $request->sort_by_price);
        if ($request->has('user_id'))
                $queryset = $queryset->where('user_id', $request->user_id);
        if ($request->has('low_limit_price'))
                $queryset = $queryset->whereRaw(
                    $last_selling_price_subquery.' >= '.$request->low_limit_price);
        if ($request->has('high_limit_price'))
            $queryset = $queryset->whereRaw(
                $last_selling_price_subquery.' <= '.$request->high_limit_price);
        if ($request->has('search'))
                $queryset = $queryset->where('description', 'like', '%'.$request->search.'%');

        $queryset = $queryset->orderBy('created_at', 'desc');
        $queryset = $queryset->paginate(10);
        return ProductResource::collection($queryset);
    }
    public function show(ProductRequest $request, Product $product) 
    {
        return new ProductResource($product);
    }
    public function store(ProductRequest $request) 
    {
        $validated_data = $request->validated();
        $validated_data['user_id'] = auth()->user()->id;

        $pictures = $validated_data['pictures'];
        unset($validated_data['pictures']);
        $price = $validated_data['price'];
        unset($validated_data['price']);

        if (sizeof($pictures) > 3) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pictures' => [__('lang.Pictures most not be more than 3')],
            ]);          
        } else if (sizeof($pictures) < 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pictures' => [__('lang.Pictures most not be less than 1')],
            ]);          
        }
        $new_product = Product::create($validated_data);
        ProductPrice::create($price + ['product_id'=>$new_product->id]);
        foreach ($pictures as $picture) {
            $a = ProductPicture::save_and_create(
                $picture['file'], $new_product->id,
            );
        }

        return new ProductResource($new_product);
    }
    
    public function update(ProductRequest $request, Product $product) 
    { # this function only updates the post not it files
        $validated_data = $request->validated();

        $product->update($validated_data);
        return new ProductResource($product);
    }
    public function destroy(ProductRequest $request, Product $product) 
    {
        $product->delete();
        return response()->json([], 204);
    }
    
    public function add_picture(ProductImageRequest $request, Product $product) 
    {
        $file = $request->validated()['file'];

        $old_images = $product->images;
        if (sizeof($old_images) >= 3) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pictures' => [__('lang.Pictures most not be more than 3')],
            ]);          
        }

        return response()->json([
            'data'=> ProductPicture::save_and_create(
                $file, $product->id,
            )
        ]);
    }
    public function delete_picture(ProductImageRequest $request, Product $product, ProductPicture $picture)
    {
        $picture->delete();
        return response()->json([], 204);
    }
    public function update_price(ProductPriceRequest $request, Product $product) 
    {
        $validated_data = $request->validated();

        return response()->json([
            'data' => ProductPrice::create($validated_data+['product_id'=>$product->id]),
        ]);
    }
}
