<?php

namespace App\Http\Controllers\Panel;

use App\User;
use App\Models\TblModels;
use App\Models\TblViews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Models\TblPackages;

class Model_Profile extends Controller
{
	public function profile(Request $request, $profile_name) {
        $data = array();
        $image_array = array();
        $profile_images = array();
        $params = $request->all();

        $image_array[1]['original'] = 'no-image.jpg'; 
        $image_array[1]['thumbnail'] = 'no-image.jpg'; 
        $image_array[2]['original'] = 'no-image.jpg'; 
        $image_array[2]['thumbnail'] = 'no-image.jpg'; 
        $image_array[3]['original'] = 'no-image.jpg'; 
        $image_array[3]['thumbnail'] = 'no-image.jpg'; 
        $image_array[4]['original'] = 'no-image.jpg'; 
        $image_array[4]['thumbnail'] = 'no-image.jpg'; 
        $image_array[5]['original'] = 'no-image.jpg'; 
        $image_array[5]['thumbnail'] = 'no-image.jpg'; 

        if(isset($params['profile_name']))
            $profile_name = $params['profile_name'];
        
        $model = TblModels::where(array('profile_name'=>$profile_name))->first();
        $images = TblModels::get_model_images(array('profile_name'=>$profile_name));
        foreach (json_decode($images) as $image) {
            $image_array[$image->img_num]['original'] = $image->image_name;
            $image_array[$image->img_num]['thumbnail'] = $image->img_num == 1 ? $image->thumbnail_image_name : $image->filename.'_thumb_'.config('image.image_size_types.small_pic').'.'.$image->file_ext;
        }
        
        $profile_images = $image_array;

        $date_today = gmdate('Y-m-d');
        $packages = TblModels::get_model_packages(['model_id' => $model->id, 'package_type' => 'snapchat']);        
        $default_pack = TblModels::get_default_package(['profile_name' => $profile_name, 'package_type' => config('api.subscription.package_type.snapchat')]);
        $data['page_title'] = 'Model Profile | xPremiums';
        $data['model'] = array("model_info" => $model, "images" => $profile_images, "packages" => json_decode($packages));
        $data['default_pack'] = $default_pack;
        $data['default_pack_instagram'] = TblModels::get_default_package(['profile_name' => $profile_name, 'package_type' => config('api.subscription.package_type.instagram')]);

        //View
        $visits = TblViews::where('ip', '=', $request->ip())->where('date_created', 'LIKE', "%{$date_today}%")->where('model_id', '=', $model->id)->get();
        if(count($visits) == 0) {
            $visit = new TblViews();
            $visit->model_id = $model->id;
            $visit->ip = $request->ip();
            $visit->user_agent = $request->header('User-Agent');
            $visit->date_created = gmdate('Y-m-d H:i:s');
            $visit->save();
        } else {
            //Do nothing
        }
        // \Dragon::vd($data['model']);    
        return view('model-profile', $data);
        
    }
}