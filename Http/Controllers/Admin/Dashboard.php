<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ProtectedPageController;
use App\Models\TblUsers;
use App\Models\TblModels;
use App\Models\TblSubscriber;
use App\Models\TblActivityLogs;

class Dashboard extends ProtectedPageController {
	public function index(){
		$date_today = gmdate('Y-m-d');
		$data['new_models'] = count(TblModels::get_models(['status'=>'pending'])->toArray());
		$data['total_models'] = count(TblModels::where('status', '=', 'active')->get());
		$data['subscribers'] = count(TblSubscriber::where('date_created', 'LIKE', "%{$date_today}%")->get());
		$data['activity_logs'] = count(TblActivityLogs::where('date_created', 'LIKE', "%{$date_today}%")->get());
		// die(var_dump($data['subscribers']));
    	return view('admin/panel/dashboard', $data); 
	}
}