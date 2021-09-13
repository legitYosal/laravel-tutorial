<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    //

    public function index(Request $request) {
        return Category::all();
    }
    public function show(Request $request, Category $category) {
        return $category;
    }
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:256', 'unique:categories'],
            'description' => ['sometimes', 'max:512'],
            // 'pictures.*.file' => ['required'], # this is nested validator
        ]);
        $validator->validate();
        $validated_data = $validator->validated();
        # use unset to delete extra validated files
        return Category::create($validated_data);
    }
    public function update(Request $request, Category $category) {

        $category->update($request->all());
        return $category;
    }
    public function destroy(Request $request, Category $category) {
        $category->delete();
        return 204;
    }
}
