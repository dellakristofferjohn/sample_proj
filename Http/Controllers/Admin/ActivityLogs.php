<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ProtectedPageController;
use App\Models\TblActivityLogs;

class ActivityLogs extends ProtectedPageController {
	public function index(Request $request){
		$data = [];
		$data['start_date'] = date('Y-m-01 00:00:00');
		$data['end_date'] = date('Y-m-t 23:59:59');

		if ($request->isMethod('post')) {
			$params = $request->all();
			\Session::put('activitylogs_from_date', $params['from']);
			\Session::put('activitylogs_to_date', $params['to']);
			$data['activity_logs'] = TblActivityLogs::get_activities($params);
		} else {
			$data['activity_logs'] = TblActivityLogs::get_activities();
		}

		//$data['activity_logs'] =TblActivityLogs::orderBy('date_created')->get();
		// die(var_dump(json_decode($data['activity_logs'])));
  return view('admin/panel/activitylogs', $data); 
	}
}