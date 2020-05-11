<?php

namespace App\Http\Controllers\api;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends ApiResponseController
{
    public function index(){
        return $this->successResponse(Category::paginate(10));
    }

    public function all(){
        return $this->successResponse(Category::all());
    }
}
