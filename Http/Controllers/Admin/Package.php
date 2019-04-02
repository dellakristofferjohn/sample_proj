<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblDefaultPackages;
use App\Models\TblPackages;

class Package extends Controller {

	public function index(){	
		$data['package_list'] = TblDefaultPackages::get();
		$data['title'] = 'Default Package';

    	return view('admin/panel/package', $data); 
	}

	public function get_pending() {
		$data['packages'] = TblPackages::get_pending_packages();
		// \Dragon::vd($data);
		return view('admin/panel/pendingpackages', $data);
	}

	// public function post_pending() {
	// 	$data['packages'] = TblPackages::get_pending_packages();

	// 	return $data;
	// }

	public function save_pending(Request $request) {
		var_dump('save me');
		$data = $request->all();
		DB::table('tbl_packages')
            ->where('id', $data['pack_id'])
            ->update(['status' => 'active', 'prod_id' => $data['prod']]);
		// \Dragon::vd($data); 
	}
	
}