<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;
use App\Http\Controllers\ProtectedPageController;
use App\Models\TblUsers;

class Dashboard extends ProtectedPageController {
	public function index(){
		$data = [
    		'page_title' => 'Dashboard',
    		'username' =>session()->get('user')['username']
    	];
    	return view('panel/users', $data); 
	}

}