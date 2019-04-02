<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ProtectedPageController;
use App\Models\TblUsers;
use App\Models\TblViews;

class Visits extends ProtectedPageController {
	public function index(Request $request){

		$data = [];
		$data['start_date'] = date('Y-m-01 00:00:00');
		$data['end_date'] = date('Y-m-t 23:59:59');

		if ($request->isMethod('post')) {
			$params = $request->all();
			\Session::put('views_from_date', $params['from']);
			\Session::put('views_to_date', $params['to']);
			$data['views'] = TblViews::get_visits($params)->toArray();
		} else {
			$data['views'] = TblViews::get_visits()->toArray();
		}

  return view('admin/panel/visits',$data); 
	}
}