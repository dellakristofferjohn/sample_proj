<?php

namespace App\Http\Controllers\Api;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblModels;
use App\Helpers\UtilHelper;
use App\Models\TblSubscription;

class Model_Subscribers extends Controller
{
    public function get_subs(Request $request)
    {
        $data = array();
        $sub = array();
        $subscriptions = array();
        $profile_name = session()->get('user')['profile_name'];
        $profile_id = session()->get('user')['id'];
        $subscribers = json_decode(\App\Models\TblModels::get_model_subscribers(array('user_id'=>$$profile_id)));
        foreach($subscribers as $subscriber) {
            if($subscriber->status == 'active') {
            } elseif ($subscriber->status == 'pending') {
                $subscriber->status = 'updates';
            } else {
                $subscriber->status = 'expired';
            }
            $subscriptions[] = array("sub_to"=>$subscriber->sub_to, "status"=>$subscriber->status, "sub_offer"=>$subscriber->sub_offer,"join_date"=>$subscriber->join_date,"exp_date"=>$subscriber->exp_date);   
            $sub[] = array("sub_id"=>$subscriber->sub_id, "username"=>$subscriber->username, "subscriptions"=>$subscriptions);
            array_pop($sub);
            $sub[] = array("sub_id"=>$subscriber->sub_id, "username"=>$subscriber->username, "subscriptions"=>$subscriptions);
            unset($subscriptions);
        }
        // $data['dashboard_subscribers'] = array("sub_info" => $subscribers);
        // \Dragon::vd($sub);
        // return $data;
    }  

}
