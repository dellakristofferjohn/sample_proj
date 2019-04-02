<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TblCategories;

class Category extends Controller
{
    public function create_category(Request $request)
    {
        $data = (array) $request->all();
        $validator = $this->validate_request($data);

        $result = [];

        if($validator->fails()){
            $result['error'] = 1;
            $result['message'] = $validator->errors()->toArray();
        }else{
            $category = new TblCategories();            
            $category->date_created = gmdate('Y-m-d H:i:s');
            $category->name = $data['category'];
            $category->description = $data['description'];
            $category->status = 1;
            $category->save();
            
            $result['error'] = 0;
            $result['message'] = 'success';
        }

        return $result;
    }

    public function validate_request($request)
    {
        $input = [
            'category' => 'required',
            'description' => 'required',
        ];

        $custom_error_msg = [
            'category.required' => 'Category is required.',
            'description.required' => 'Description is required.',
        ];

        $validator = \Validator::make($request, $input, $custom_error_msg);

        return $validator;
    }
}
