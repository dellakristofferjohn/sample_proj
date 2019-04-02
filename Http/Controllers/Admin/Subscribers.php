<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ProtectedPageController;
use App\Models\TblUsers;
use App\Models\TblModels;

class Subscribers extends ProtectedPageController {
	public function index(Request $request){
		$data = [];
		$data['start_date'] = date('Y-m-01 00:00:00');
		$data['end_date'] = date('Y-m-t 23:59:59');

		$data['search_type'] = array(
					"date_started"			=> "Date Started",
					"expiration_date"=> "Expiration"
				);

		$data['status'] = array(
					"0" 						=> 'All',
					"active"		=> "Active",
					"pending"	=> "Pending",
					"expired"	=> "Expired"
				);

		$data['offer_type'] = array(
					"0" 						=> 'All',
					"snapchat"		=> "Snapchat",
					"instagram"	=> "Instagram",
				);

		if ($request->isMethod('post')) {
			$params = $request->all();
			\Session::put('subscribers_from_date', $params['from']);
			\Session::put('subscribers_to_date', $params['to']);
			\Session::put('subscribers_search_type', $params['search_type']);
			\Session::put('subscriber_status', $params['status']);
			\Session::put('subscriber_offer_type', $params['offer_type']);
			$data['subscribers'] = TblModels::get_model_with_subscribers($params);
		} else {
			$data['subscribers'] = TblModels::get_model_with_subscribers();
		}
    return view('admin/panel/subscribers',$data); 
	}
}