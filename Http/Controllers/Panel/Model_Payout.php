<?php

namespace App\Http\Controllers\Panel;

use DateTime;
use App\User;
use App\Models\TblCategories;
use App\Models\TblSubscriber;
use App\Models\TblSubscription;
use App\Models\TblSubscriptionLog;
use App\Models\TblModels;
use App\Models\TblPayouts;
use App\Models\TblAffiliateTracker;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;
use App\Http\Controllers\ProtectedPageController;

class Model_Payout extends ProtectedPageController
{
	public function index(){
		$data = [];
		$data['from'] = ( !is_null(session()->get('model_transaction_from')) ? session()->get('model_transaction_from') : date('M 01 Y'));
		$data['to'] = ( !is_null(session()->get('model_transaction_to')) ? session()->get('model_transaction_to') : date('M t Y'));

		$data['model'] = TblModels::find(session()->get('user')['model_id']);
		
		return view('panel/model/payout', $data);
	}

	public function get_payouts(Request $request){
		$data = $request->all();

		$data['user_id'] = session()->get('user')['id'];

		$data['from'] = \App\Helpers\DateTimeHelper::format_date($data['from'], config('application.to_timezone'), config('application.from_timezone'), "Y-m-d");
		$data['to'] = \App\Helpers\DateTimeHelper::format_date($data['to'], config('application.to_timezone'), config('application.from_timezone'), "Y-m-d");

		$payout = TblPayouts::get_model_payout($data)->toArray();
		
		$data['limitOff'] = true;

		$noPages = TblPayouts::get_model_payout($data)->count();

		$results = [];

		\Session::put('model_payout_from', $data['from']);
		\Session::put('model_payout_to', $data['to']);

		$results['offset'] = $data['offset']; 
		$results['noPage'] = round($noPages / $data['limit']);
		$results['payout'] = $payout; 

		return json_encode($results);
	}

}