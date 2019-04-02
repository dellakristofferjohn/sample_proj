<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TblPaymentDetail;
use App\Models\TblModels;

class PaymentDetail extends Controller
{
    public function createOrUpdatePayment(Request $request)
    {
        $session_user = session()->get('user');
        $model = TblModels::where(array('user_id' => $session_user['id']))->first();
        $request_data = $request->all();

        $data['model_id'] = $model->id;
        $data['payment_method'] = $request_data['payment_method'];
        $data['minimum_payout'] = $request_data['minimum_payout'];
        $data['payment_details'] = json_encode($request_data);
        $data['date_created'] = gmdate("Y-m-d H:i:s");
        
        TblPaymentDetail::updateOrCreate(
            array(
                'model_id' => $model->id
            ),
            $data
        );

        $result['status'] = 1;
        $result['message'] = 'success';

        return $result;
    }
}
