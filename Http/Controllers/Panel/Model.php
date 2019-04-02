<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TblModels;

class Model extends Controller {

	public function index(){	
		$data['models'] = TblModels::get();
		$data['title'] = 'Model List';

    	return view('panel/model', $data); 
	}
	
}