<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblLinks;

class Links extends Controller
{
    public function create_links(Request $request){
    	$data = (array) $request->all();

        $validator = $this->validate_request($data);

        $result = [];

        if($validator->fails()){
            $result['error'] = 1;
            $result['message'] = $validator->errors()->toArray();
        }else{
        	$link = new TblLinks();
            $link->name = $data['link_label'];
            $link->initial_value = $data['link_initial_value'];
            $link->website = $data['website'];
            $link->is_default = isset($data['stat_default']) ? true : false;
        	$link->date_created = gmdate('Y-m-d H:i:s');
        	$link->save();

            $result['error'] = 0;
         	$result['message'] = 'success';
        }

        return $result;
    }

    public function validate_request($request)
    {
    	$input = [
    		'link_label' => 'required',
    	];

    	$custom_error_msg = [
    		'link_label.required' => 'Stat Label is required.',
    	];

    	$validator = \Validator::make($request, $input, $custom_error_msg);

    	return $validator;
    }
}
