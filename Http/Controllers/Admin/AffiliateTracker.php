<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ProtectedPageController;
use App\Models\TblAffiliateTracker;

class AffiliateTracker extends ProtectedPageController {
	public function index(){
		$data['affiliates'] = TblAffiliateTracker::get_affiliate()->toArray();
    	return view('admin/panel/affiliate',$data); 
	}
}