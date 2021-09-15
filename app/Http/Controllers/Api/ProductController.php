<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Product;
use App\Models\ProductPicture;
use App\Models\ProductPrice;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    //
    public function index(Request $request) 
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

        $validator = Validator::make($filtering_params, [
            'user_id' => ['sometimes', 'exists:users,id'],
            'sort_by_price' => ['sometimes', 'in:desc,asc'],
            'low_limit_price' => ['sometimes', 'integer'],
            'high_limit_price' => ['sometimes', 'integer'],
            'search' => ['sometimes', 'string', ]
        ]);
        $validator->validate();
        $filters = $validator->validated();

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

        $queryset = $queryset->orderBy('created_at', 'desc') -> get();
        // $queryset = $queryset->paginate(10);
        return $queryset;
        return ProductResource::collection($queryset);
    }
    public function show(Request $request, Product $product) 
    {
        return new ProductResource($product);
    }
    public function store(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'max:256'],
            'description' => ['required', 'max:2048'],
            'pictures' => ['required'],
            'pictures.*.file' => ['required', 
                    'mimes:jpeg,png,jpg,gif,svg', 
                    'image', 'max:2048'], 
            // 'price' => ['required'],
            'price.bought_price' => ['required', 'integer'],
            'price.selling_price' => ['required', 'integer'],
        ]);
        $validator->validate();
        $validated_data = $validator->validated();
        $validated_data['user_id'] = auth()->user()->id;

        $pictures = $validated_data['pictures'];
        unset($validated_data['pictures']);
        $price = $validated_data['price'];
        unset($validated_data['price']);

        if (sizeof($pictures) > 3) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pictures' => ['Pictures most not be more than 3'],
            ]);          
        } else if (sizeof($pictures) < 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pictures' => ['Pictures most not be less than 1'],
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
    
    public function update(Request $request, Product $product) 
    { # this function only updates the post not it files
        if (auth()->user()->id !== $product->user_id) {
            return response()->json([
                'message' => 'You are not authorized to access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['sometimes', 'max:256'],
            'description' => ['sometimes', 'max:2048'],
            // 'pictures.*.file' => ['required', 'mimes:jpeg,png,jpg,gif,svg', 'image', 'max:2048'], 
        ]);
        $validator->validate();
        $validated_data = $validator->validated();

        // if (array_key_exists('pictures', $validated_data)) {
        //     $pictures = $validated_data['pictures'];
        //     unset($validated_data['pictures']);

        // }
        $product->update($validated_data);
        return new ProductResource($product);
        // $category->update($request->all());
        // return $category;
    }
    public function destroy(Request $request, Product $product) 
    {
        if (auth()->user()->id !== $product->user_id) {
            return response()->json([
                'message' => 'You are not authorized to access'
            ], 403);
        }
        $product->delete();
        return response()->json([], 204);
    }
    
    public function add_picture(Request $request, Product $product) 
    {
        if (auth()->user()->id !== $product->user_id) {
            return response()->json([
                'message' => 'You are not authorized to access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'file' => ['required', 'mimes:jpeg,png,jpg,gif,svg', 'image', 'max:2048'], 
        ]);
        $validator->validate();
        $file = $validator->validated()['file'];

        $old_images = $product->images;
        if (sizeof($old_images) >= 3) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pictures' => ['Pictures most not be more than 3'],
            ]);          
        }

        return response()->json([
            'data'=> ProductPicture::save_and_create(
                $file, $product->id,
            )
        ]);
        

        // return new ProductResource($product);
    }
    public function delete_picture(Request $request, Product $product, ProductPicture $picture)
    {
        if ($picture->product_id !== $product->id || $product->user_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to access'
            ], 403);
        }
        $picture->delete();
        return response()->json([], 204);
    }
    public function update_price(Request $request, Product $product) 
    {
        if (auth()->user()->id !== $product->user_id) {
            return response()->json([
                'message' => 'You are not authorized to access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'bought_price' => ['integer', 'required'],
            'selling_price' => ['integer', 'required'],
        ]);
        $validator->validate();
        $validated_data = $validator->validated();

        return response()->json([
            'data' => ProductPrice::create($validated_data+['product_id'=>$product->id]),
        ]);
    }
}
