<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TblCategories;

class Category extends Controller {

	public function index(){	
		$data['categories'] = TblCategories::get();
		$data['title'] = 'Categories';

    return view('admin/panel/categories', $data); 
	}
	
}