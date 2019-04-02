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
use App\Models\TblImages;
use App\Models\TblAffiliateTracker;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;
use App\Http\Controllers\ProtectedPageController;

class Model_Myprofile extends ProtectedPageController
{
    public function index(){    
        $modelId = session()->get('user')['model_id'];
        $data['model'] = TblModels::find($modelId);
        $data['image'] = TblImages::where(array('model_id'=>$modelId,'img_num'=>6))->first();
        $data['title'] = 'My Profile';
        return view('panel/model/myprofile', $data); 
    }

    public function add_screen_name(Request $request) {
        $modelId = session()->get('user')['model_id'];
        $model = TblModels::find($modelId);
        $model->screen_name = $request->input('screenName');
        $model->save();
    }

    public function add_avatar(Request $request){
        $model_id = $request->segment(4);
        $photo_num = 6;
        $image = $request->input('fd');
        $filename = basename($image);
        $imageModelName = $filename;

        $model = TblModels::where(array('id'=>$model_id))->first();

        $destinationPath = public_path().'/uploads/'.$model->profile_name;

        if (!file_exists($destinationPath.'/avatar')) {
            mkdir($destinationPath.'/avatar', 0777, true);
        }

        $photos = TblImages::where(array('model_id'=>$model->id,'img_num'=>$photo_num))->get();
        if (count($photos) > 0){
            foreach ($photos as $photo) {
                if(file_exists($destinationPath.'/avatar/'.$photo->name))
                    unlink($destinationPath.'/avatar/'.$photo->name);

                $photo->delete();
            }
        }

        $saveImage = TblImages::updateOrCreate(['name' => $imageModelName]);
        $saveImage->model_id = $model_id; //session()->get('user')['model_id'];
        $saveImage->name = $imageModelName;
        $saveImage->img_num = $photo_num;
        $saveImage->status = 1;
        $saveImage->date_created = gmdate('Y-m-d H:i:s');
        $saveImage->thumbnail_name = 'no.jpg';
        $saveImage->save();
        $input['imagename'] = $imageModelName;

        $avatar_src = $destinationPath.'/avatar';

        $user = $request->session()->get('user');
        $user['avatar'] = $imageModelName;
        session(['user' => $user]);

		\Image::make($image)->save($avatar_src . '/' . $filename);
        $return_arr = array("name" => $imageModelName, "src"=> $avatar_src);
        echo json_encode($return_arr);
    }

    public function del_avatar(Request $request){
        $model_id = $request->segment(4);
        $photo_num = 6;
        $model = TblModels::where(array('id'=>$model_id))->first();
        $photos = TblImages::where(array('model_id'=>$model_id,'img_num'=>$photo_num))->get();
        $destinationPath = public_path().'/uploads/'.$model->profile_name;
        if (count($photos) > 0){
            foreach ($photos as $photo) {
                if(file_exists($destinationPath.'/avatar/'.$photo->name))
                    unlink($destinationPath.'/avatar/'.$photo->name);

                $photo->delete();
            }
        }
        echo $photo;
    }

}