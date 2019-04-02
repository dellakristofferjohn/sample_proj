<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TblLinks;

class Links extends Controller {

	public function index(){	
		$data['links'] = TblLinks::get();
		$data['title'] = 'Links';

    return view('admin/panel/links', $data); 
	}
	
}