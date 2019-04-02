<?php

namespace App\Http\Controllers\Panel;

use DateTime;
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
use App\Http\Controllers\ProtectedPageController;

class Model_Dashboard extends ProtectedPageController
{
    public function get_dashboard_info() {
        $data = array();
        $active_subs = array();
        $new_subs = array();
        $cancelled_subs = array();
        $week_exp_subs = array();
        $pending_subs = array();
        $profile_name = session()->get('user')['profile_name'];
        $profile_id = session()->get('user')['id'];
        $ttl = 0;
        $model = TblModels::get_model_info(array('profile_name'=>$profile_name));
        $subscribers = TblModels::get_model_with_subscribers(array('profile_name'=>$profile_name));
        $current_date = new DateTime(date('Y-m-d'));

        foreach (json_decode($subscribers) as $subscriber) {
            $FirstDay = date("Y-m-d H:i:s", strtotime('sunday last week')); 
            $LastDay = date("Y-m-d 23:59:59", strtotime('sunday this week'));
            
            if($subscriber->date_created > $FirstDay && $subscriber->date_created < $LastDay && $subscriber->status == 'active') {
                $new_subs[] = $subscriber->snapchat_id;
                $active_subs[] = $subscriber->snapchat_id;
            } else if ($subscriber->status == 'active') {
                $active_subs[] = $subscriber->snapchat_id;
            } else if ($subscriber->date_created > $FirstDay && $subscriber->date_created < $LastDay && $subscriber->status == 'expired') {
                $week_exp_subs[] = $subscriber->snapchat_id;
            } else if($subscriber->status == 'pending') {
                $pending_subs[] = $subscriber;
            } else {
                $cancelled_subs[] = $subscriber->snapchat_id;
            }
        }
        // $revenue = \App\Models\TblSubscription::get_transactions(array('user_id'=>$profile_id));
        $revenue = \App\Models\TblCcBillSubscription::get_transactions(array('user_id'=>$profile_id));
        
        foreach($revenue as $rev) {
            $ttl = $ttl +$rev->net;
        }

        $views = \App\Models\TblModels::get_model_views(array('profile_name'=>$profile_name)); 
        $data['dashboard_info'] = array("model_info" => $model, "active_subs" => count($active_subs), "new_subs" => count($new_subs), "cancelled_subs" => count($cancelled_subs), "week_exp_subs" => count($week_exp_subs), "pending_subs" => $pending_subs, "revenue" => $ttl, "views" => $views);
        // \Dragon::vd($data['dashboard_info']['model_info']['result']['model_info']->id);

        return view('panel/model-dashboard', $data);
    }

    public function get_dashboard_subscribers(Request $request) {
        $data = array();
        $profile_name = session()->get('user')['profile_name'];
        $model = TblModels::get_model_info(array('profile_name'=>$profile_name));
        $data['sub_id'] = $model['result']['model_info']->id;
        $data['model'] = $model['result']['model_info'];
        return view('panel/model-subscribers', $data);
    }

    public function get_dashboard_transactions(Request $request) {
        $data = array();
        $params = $request->all();
        
        $profile_name = $params['profile_name'];
        $transactions = \App\Models\TblSubscription::get_subscriptions(array('profile_name'=>$profile_name));
        $data['dashboard_transactions'] = array("transactions" => json_decode($transactions));

        return view('model-dashboard', $data);
        // die(var_dump('<pre>',json_decode($transactions),'</pre>'));    
    }

}