<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblStats;

class Stats extends Controller
{
    public function create_stat(Request $request){
    	$data = (array) $request->all();
        $validator = $this->validate_request($data);

        $result = [];        
        if($validator->fails()){
            $result['error'] = 1;
            $result['message'] = $validator->errors()->toArray();
        }else{
        	$stat = new TblStats();
            $stat->label = $data['stat_label'];
            $stat->initial_value = $data['stat_initial_value'];
            $stat->is_default = isset($data['stat_default']) ? true : false;
        	$stat->date_created = gmdate('Y-m-d H:i:s');
        	$stat->save();

            $result['error'] = 0;
         	$result['message'] = 'success';
        }

        return $result;
    }

    public function validate_request($request)
    {
    	$input = [
    		'stat_label' => 'required',
    	];

    	$custom_error_msg = [
    		'stat_label.required' => 'Stat Label is required.',
    	];

    	$validator = \Validator::make($request, $input, $custom_error_msg);

    	return $validator;
    }
}
