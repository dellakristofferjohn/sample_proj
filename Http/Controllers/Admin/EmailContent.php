<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TblCategories;

class EmailContent extends Controller {

	public function index(){	
		$data['categories'] = TblCategories::get();
		$data['title'] = 'Categories';

    return view('admin/panel/email_content', $data); 
	}

	public function add_content(Request $req){	
		die(var_dump($req->all()));

	}
	
}