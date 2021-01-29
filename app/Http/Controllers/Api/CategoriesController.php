<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Repositories\Models\Category;

class CategoriesController extends Controller
{
    public function index()
    {
        CategoryResource::wrap('data');
    	return CategoryResource::collection(Category::all());
    }
}
