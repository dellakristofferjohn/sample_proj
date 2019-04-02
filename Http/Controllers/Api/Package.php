<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblDefaultPackages;

class Package extends Controller
{
    public function create_package(Request $request){
    	$data = (array) $request->all();
        $validator = $this->validate_request($data);

        $result = [];

        if($validator->fails()){
            $result['error'] = 1;
            $result['message'] = $validator->errors()->toArray();
        }else{
        	TblDefaultPackages::add_package($data);

            $result['error'] = 0;
         	$result['message'] = 'success';
        }

        return $result;
    }

    public function validate_request($request)
    {
    	$input = [
            'package_type' => 'required',
    		'option_name' => 'required',
    		'option_type' => 'required',
    		'option_description' => 'required',
            'price_period' => 'required|numeric',
    	];

    	$custom_error_msg = [
            'package_type.required' => 'Package type is required.',
    		'option_name.required' => 'Option Name is required.',
            'option_type.required' => 'Option Type is required.',
            'option_description.required' => 'Description is required.',
            'price_period.required' => 'Option Period is required.',
            'price_period.numeric' => 'Option Period must be a number',
    	];

    	$validator = \Validator::make($request, $input, $custom_error_msg);

    	return $validator;
    }
}
