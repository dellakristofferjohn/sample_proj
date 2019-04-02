<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\TblCategories;
use App\Models\TblSubscriber;
use App\Models\TblSubscription;
use App\Models\TblSubscriptionLog;
use App\Models\TblModels;
use App\Models\TblViews;
use App\Models\TblAffiliateTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;

class Admin extends Controller
{
    public function subscribers() {
        $subscribers = \App\Models\TblSubscriber::get();
        die(var_dump('<pre>',json_decode($subscribers),'</pre>'));
    }

    public function views() {
        $views = \App\Models\TblViews::get();
        die(var_dump('<pre>',json_decode($views),'</pre>'));
    }

    public function affiliates() {
        $affiliates = \App\Models\TblAffiliateTracker::get();
        die(var_dump('<pre>',json_decode($affiliates),'</pre>'));
    }

    public function transactions() {
        $subscriptionLog = \App\Models\TblSubscriptionLog::get();
        $subscription = new TblSubscription();
        if (count($subscriptionLog) > 0){
            foreach ($subscriptionLog as $log) {
                $meta = json_decode($log->meta);
                $response = json_decode($log->response);
                $date_created = $log->date_created;
                if(isset($response->bill_id)) {
                    $subscription_data = $subscription->get_subscriptions(array("bill_id"=>$response->bill_id,));
                    foreach ($subscription_data as $key) {
                        var_dump('Success');
                        var_dump('<pre>',$key->bill_id,$key->subscriber_id,$key->package_name,$key->profile_name,$key->price,$key->date_started,$key->expiration_date,'</pre>');
                    }
                    // var_dump('<pre>',$response->bill_id,$date_created,'</pre>');
                    // var_dump('<pre>',$meta->email,$response->result,$date_created,'</pre>');    
                    // $subscriber = \App\Models\TblSubscriber::where(array('email'=>$meta->email))->get();
                    // var_dump('<pre>',json_decode($subscriber),'</pre>');
                } else {
                    var_dump('error');
                    // var_dump('<pre>',$response->errors,$date_created,'</pre>');
                }
            }
            die();
        }
    }

    public function models_with_subscribers() {
        $data = array();
        $model = new TblModels();

        $model_info = $model->get_model_with_subscribers();
        die(var_dump('<pre>',$model_info,'</pre>'));
    }

}