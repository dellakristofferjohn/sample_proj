<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TblSubscription;
use Illuminate\Support\Facades\DB;

class SubscriptionNotification extends Controller{
    public function updateAppSubscriptionDetails(){
        $subscriptions = \DB::table('tbl_subscriptions')->select(DB::raw('*'))
                  ->whereRaw('Date(expiration_date) = CURDATE()')->where(array('status'=>true))->get();
                  
        if(count($subscriptions) > 0){
            foreach ($subscriptions as $subscription) {
                $currentDate = $subscription->expiration_date;
                $bill_id = $subscription->bill_id;
                $prod_id = $subscription->prod_id;
                
                $customer_record = app(\App\Http\Controllers\Api\Subscription::class)->customer_search($bill_id);
                
                $currentSubscription = TblSubscription::find($subscription->id);
                if ($customer_record['customers'][0]['orders'][0]['order_stat'] == 'cancelled')
                {
                    $currentSubscription->status = false;
                    $currentSubscription->save();
                }
            }
            var_dump('success');
        }else{
            var_dump('no expired subscription today');
        }
        var_dump(gmdate('Y-m-d H:i:s'));
        die();
    }
}