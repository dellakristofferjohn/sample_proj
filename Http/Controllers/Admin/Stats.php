<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TblStats;

class Stats extends Controller {

	public function index(){	
		$data['stats'] = TblStats::get();
		$data['title'] = 'Stats';

    return view('admin/panel/stats', $data); 
	}
	
}