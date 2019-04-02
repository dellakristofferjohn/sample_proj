<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblPayouts;
use DateTime;

class Payouts extends Controller {
	
	public function index(Request $request) {
		$data = [];
		$data['start_date'] = date('Y-m-01 00:00:00');
		$data['end_date'] = date('Y-m-t 23:59:59');

		$data['payouts_status'] = array(
			"processing"	=> "processing",
			"approved"	=> "approved"
		);

		if ($request->isMethod('post')) {
			$params = $request->all();
			\Session::put('payouts_from_date', $params['from']);
			\Session::put('payouts_to_date', $params['to']);
			\Session::put('payouts_status', $params['status']);
			\Session::put('current_status', $params['status']);
			$data['payouts'] = TblPayouts::get_payouts($params);
		} else {
			\Session::put('current_status', 'processing');
			$data['payouts'] = TblPayouts::get_payouts();
		}
		return view('admin/panel/payouts', $data);
	}

	public function save_payouts(Request $request) {
		$current_date = gmdate("Y-m-d H:i:s");
        $params = $request->all();
        DB::table('tbl_payouts')
            ->where('id', $params['po_id'])
            ->update(['statement' => 'paid', 'status' => 'approved', 'approved_date' => $current_date]);
	}

	public function remove_payouts(Request $request) {
		$current_date = gmdate("Y-m-d H:i:s");
        $params = $request->all();
        DB::table('tbl_payouts')
            ->where('id', $params['po_id'])
            ->update(['statement' => 'pending', 'status' => 'processing', 'approved_date' => null]);
	}
}