<?php

namespace App\Http\Controllers\Panel;

use DateTime;
use App\User;
use App\Models\TblModels;
use App\Models\TblPackages;
use App\Models\TblDefaultPackages;
use App\Models\TblAffiliateTracker;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;
use App\Http\Controllers\ProtectedPageController;

class Model_Subscriptions extends ProtectedPageController
{
    public function index() {    
    	$model_id = session()->get('user')['model_id'];
        $data['models'] = TblModels::where(['id' => $model_id])->first();
        $data['packages_snap'] = TblPackages::where(['model_id' => $model_id, 'package_type' => 'snapchat'])->get();
        $data['packages_ins'] = TblPackages::where(['model_id' => $model_id, 'package_type' => 'instagram'])->get();

        $data['title'] = 'Subscriptions offers';

        return view('panel/model/subscriptions', $data); 
    }

    public function get_subs(Request $request) {
        $data = $request->all();
        $sub = array();
        $data['user_id'] = session()->get('user')['id'];
        $subscribers = json_decode(\App\Models\TblModels::get_model_subscribers($data));
        $sub = array("offset"=>$data["offset"], "noPages"=>count($subscribers)/ $data['limit'], "subscribers"=>$subscribers);        
        return $sub;
    }

    public function get_pending_subs(Request $request) {
        $data = $request->all();
        $subs = array();
        $subscribers['subs'] = json_decode(\App\Models\TblModels::get_model_subscribers(array('user_id'=>session()->get('user')['id'])));
        $datetimeNow = gmdate('Y-m-d H:i:s');
        $subscribers['date_now'] = $datetimeNow;
        // foreach($subscribers as $subscriber) {
        //     if ($subscriber->status == 'pending') {
        //         $subs['pending'] = $subscriber;
        //     } elseif ($subscriber->exp_date < $datetimeNow && $subscriber->status == 'active') {
        //         $subs['expired'] = $subscriber;
        //     } else {
                
        //     }
        //     // var_dump($subscriber);
        // }
        return $subscribers;
    } 

    public function get_package_info() {
        $this->get_subscriptions_offer();
    }

    public function get_subscriptions_offer() {

        $model_id = session()->get('user')['model_id'];
        $model = TblModels::find($model_id);
        $snap_package = TblPackages::where(['model_id' => $model_id, 'package_type' => 'snapchat', 'status' => 1])->orderBy('order', 'asc')->get();
        $ins_package = TblPackages::where(['model_id' => $model_id, 'package_type' => 'instagram' , 'status' => 1])->orderBy('order', 'asc')->get();

        $snapchatArr = array('subscription' => "snapchat",
                    'empty_snap_id' => empty($model->snapchat_id ) ? 1 : 0,
                    'subscription_username' => "",
                    'subscription_status' => "",
                    'subscription_offers' => []);
        // var_dump(count($snap_package)); die();
        if (count($snap_package) > 0){
            foreach ($snap_package as $key => $value) {
                $snapSubsOffer = [
                    "offer_id" => $value->id,
                    "offer_name" => $value->name,
                    "offer_period" => $value->duration,
                    "price_period" => $value->price,
                    "price_period_curr" => $value->currency,
                    "offer_type" => $value->option_type,
                    "offer_desc" => $value->description,
                    "default" => 0,
                    "package_type" => 'snapchat'
                ];
                array_push($snapchatArr['subscription_offers'], $snapSubsOffer);
            }
        }else {
            $snap_package_def = TblDefaultPackages::where(['package_type' => 'snapchat', 'status' => 1])->orderBy('id', 'asc')->get();
            foreach ($snap_package_def as $key => $value) {
                $snapSubsOffer = [
                    "offer_id" => $value->id,
                    "offer_name" => $value->name,
                    "offer_period" => $value->duration,
                    "price_period" => $value->price,
                    "price_period_curr" => $value->currency,
                    "offer_type" => $value->option_type,
                    "offer_desc" => $value->description,
                    "default" => 1,
                    "package_type" => 'snapchat'
                ];
                array_push($snapchatArr['subscription_offers'], $snapSubsOffer);
            }
        }

        $instagramArr = array('subscription' => "instagram",
                    'empty_ig_id' => empty($model->snapchat_id ) ? 1 : 0,
                    'subscription_username' => "",
                    'subscription_status' => "",
                    'subscription_offers' => []);

        if (count($ins_package) > 0){
            foreach ($ins_package as $key => $value) {
                $insSubsOffer = [
                    "offer_id" => $value->id,
                    "offer_name" => $value->name,
                    "offer_period" => $value->duration,
                    "price_period" => $value->price,
                    "price_period_curr" => $value->currency,
                    "offer_type" => $value->option_type,
                    "offer_desc" => $value->description,
                    "default" => 0,
                    "package_type" => 'instagram'
                ];
                array_push($instagramArr['subscription_offers'], $insSubsOffer);
            }
        }else {
            $ins_package_def = TblDefaultPackages::where(['package_type' => 'instagram' , 'status' => 1])->orderBy('id', 'asc')->get();
            foreach ($ins_package_def as $key => $value) {
                $snapSubsOffer = [
                    "offer_id" => $value->id,
                    "offer_name" => $value->name,
                    "offer_period" => $value->duration,
                    "price_period" => $value->price,
                    "price_period_curr" => $value->currency,
                    "offer_type" => $value->option_type,
                    "offer_desc" => $value->description,
                    "default" => 1,
                    "package_type" => 'instagram'
                ];
                array_push($instagramArr['subscription_offers'], $snapSubsOffer);
            }
        }

        $res = array($snapchatArr, $instagramArr);

        echo json_encode($res);
    }


    public function get_default_package_info() {

        $model_id = session()->get('user')['model_id'];
        $pckCount = TblPackages::where('model_id', $model_id)->count(); 
        if ($pckCount > 0) {
            $snap_package = TblPackages::where(['model_id' => $model_id, 'package_type' => 'snapchat', 'status' => 1])->orderBy('order', 'asc')->get();
            $ins_package = TblPackages::where(['model_id' => $model_id, 'package_type' => 'instagram' , 'status' => 1])->orderBy('order', 'asc')->get();
        }else {
            $snap_package = TblDefaultPackages::where(['package_type' => 'snapchat', 'status' => 1])->orderBy('id', 'asc')->get();
            $ins_package = TblDefaultPackages::where(['package_type' => 'instagram' , 'status' => 1])->orderBy('id', 'asc')->get();
        }

        $snapchatArr = array('subscription' => "snapchat",
                    'subscription_username' => "",
                    'subscription_status' => "",
                    'subscription_offers' => []);

        foreach ($snap_package as $key => $value) {
                $snapSubsOffer = [
                    "offer_id" => $value->id,
                    "offer_name" => $value->name,
                    "offer_period" => $value->duration,
                    "price_period" => $value->price,
                    "price_period_curr" => $value->currency,
                    "offer_type" => $value->option_type,
                    "offer_desc" => $value->description,
                    "package_type" => $value->package_type
                ];
                array_push($snapchatArr['subscription_offers'], $snapSubsOffer);
        }

        $instagramArr = array('subscription' => "instagram",
                    'subscription_username' => "",
                    'subscription_status' => "",
                    'subscription_offers' => []);

        foreach ($ins_package as $key => $value) {
                $insSubsOffer = [
                    "offer_id" => $value->id,
                    "offer_name" => $value->name,
                    "offer_period" => $value->duration,
                    "price_period" => $value->price,
                    "price_period_curr" => $value->currency,
                    "offer_type" => $value->option_type,
                    "offer_desc" => $value->description,
                    "package_type" => $value->package_type
                ];
                array_push($instagramArr['subscription_offers'], $insSubsOffer);
        }

        $res = array($snapchatArr, $instagramArr);

        echo json_encode($res);
    }


    public function edit_offer(Request $request){
    	$model_id = session()->get('user')['model_id'];
        $model = TblModels::find($model_id);
        $model->snapchat_id = $request->input('snapId');
        $model->instagram_id = $request->input('instaId');
        $model->subscriber_option = !empty($request->input('snapId')) ? 1 : 0;
        $model->instagram_option = !empty($request->input('instaId')) ? 1 : 0;
        $model->save();

        $this->get_subscriptions_offer();
    }

    public function edit_options(Request $request){
    	$model_id = session()->get('user')['model_id'];
        $model = TblModels::find($model_id);
        $model->subscriber_option = $request->input('optionSnap');
        $model->instagram_option = $request->input('optionInsta');
        $model->save();
    }

    public function add_subscription_offer(Request $request){
    	$model_id = session()->get('user')['model_id'];
        $pckCount = TblPackages::where('model_id', $model_id)->count();
    	$recurr = new TblPackages;
    	$recurr->model_id = $model_id;
    	$recurr->name = $request->input('offer_name');

    	if ($request->input('offer_period')) 
    		$recurr->duration = $request->input('offer_period');

    	$recurr->price = $request->input('price_period');
    	$recurr->description = $request->input('offer_desc');
    	$recurr->option_type = $request->input('offer_option');
    	$recurr->currency = "usd";
    	$recurr->status = 1;
    	$recurr->date_created = gmdate('Y-m-d H:i:s');
    	$recurr->order = $pckCount+1;
    	$recurr->package_type = $request->input('offer_type');
    	$recurr->save();
    	echo $recurr;
    }

    public function add_package(Request $request){
        $model_id = session()->get('user')['model_id'];
        TblPackages::where('model_id', $model_id)->delete();
        $pckCount = TblPackages::where('model_id', $model_id)->count(); 
        $offer_type = $request->input('offer_type');
        $offer_name = $request->input('offer_name');
        $option_type = $request->input('option_type');
        $offer_period = $request->input('offer_period');
        $price_period = $request->input('price_period');
        $offer_desc = $request->input('offer_desc');
        for ($i=0; $i < count($offer_type) ; $i++) { 
            $recurr = new TblPackages;
            $recurr->model_id = $model_id;
            $recurr->name = $offer_name[$i];
            $recurr->duration = $offer_period[$i];
            $recurr->price = $price_period[$i];
            $recurr->description = $offer_desc[$i];
            $recurr->option_type = $option_type[$i];
            $recurr->currency = "usd";
            $recurr->status = 1;
            $recurr->date_created = gmdate('Y-m-d H:i:s');
            $recurr->order = $i+1;
            $recurr->package_type = $offer_type[$i];
            $recurr->save();
            // echo $recurr;
        }
        echo true;
    }

    public function del_subscription_offer(Request $request){
    	$pack = TblPackages::find($request->input('formId'));
    	$pack->status = 0;
    	$pack->save();
    	echo $pack;
    }

    public function arrange_package(Request $request){
        $pckId = $request->input('id');
        $order = 1;
        foreach ($pckId as $val) {
            $pack = TblPackages::find($val);
            $pack->order = $order;
            $pack->save();
            $order++;
        }
    }

}