<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblModels;
use App\Helpers\UtilHelper;

class Model extends Controller
{
    public function get_models(Request $request)
    {
        $model = new TblModels();
        $params = $request->all();
        $params['status'] = 'active';
        $model_info = $model->get_models($params);

        return $model_info;
    }   

    public function change_model_status(Request $request)
    {
    	$data = $request->all();
        $model = TblModels::find($data['id']);
        if ($model->status == 'pending') {
            if ($data['status'] == 'active') {
                UtilHelper::email('emails.approved', 'MODEL APPROVED', ['profilename'=>$model->profile_name], ['noreply@nmscdn.net'], [$data['email']]);
            }
        }else{
            if ($data['status'] == 'active') {
                UtilHelper::email('emails.activated', 'MODEL ACTIVATED', ['profilename'=>$model->profile_name], ['noreply@nmscdn.net'], [$data['email']]);
            }
        }
        if ($data['status'] == 'block') {
            UtilHelper::email('emails.block', 'MODEL BLOCK', ['profilename'=>$model->profile_name], ['noreply@nmscdn.net'], [$data['email']]);
        }
    	$model->status = $data['status'];
        $model->save();
        
    	$status['error'] = 0;
    	$status['message'] = 'success';

    	return $status;
    }

    public function update_model(Request $request)
    {
        $model_id = session()->get('user')['model_id'];
        $data = (array) $request->all();
        $validator = $this->validate_request($data);

        $result = [];
        if($validator->fails()){
            $result['error'] = 1;
            $result['message'] = $validator->errors()->toArray();
            
        }else{            
            $model = TblModels::find($model_id);
            
            if (isset($data['email'])){
                $model->user->email = $data['email'];
            }
            if (isset($data['screen_name'])){
                $model->screen_name = $data['screen_name'];
            }
            if (isset($data['phone'])){
                $model->user->phone_number = $data['phone'];    
            }
            if(isset($data['skype'])){
                $model->skype = $data['skype'];    
            }
            if(isset($data['password'])){
                $model->user->password = bcrypt($data['password']);
            }
            if(isset($data['community_name'])){
                $model->community_name = $data['community_name'];    
            }
            if(isset($data['twitter'])){
                $model->twitter = $data['twitter'];    
            }
            if(isset($data['maiden_first_name'])){
                $model->maiden_first_name = $data['maiden_first_name'];    
            }
            if(isset($data['maiden_last_name'])){
                $model->maiden_last_name = $data['maiden_last_name'];    
            }
            if(isset($data['prev_legal_first_name'])){
                $model->prev_legal_first_name = $data['prev_legal_first_name'];    
            }
            if(isset($data['prev_legal_last_name'])){
                $model->prev_legal_last_name = $data['prev_legal_last_name'];    
            }
            if(isset($data['other_stage_name'])){
                $model->other_stage_name = $data['other_stage_name'];    
            }
            if(isset($data['date_of_birth'])){
                $model->date_of_birth = date('Y-m-d', strtotime($data['date_of_birth']));    
            }
            if(isset($data['gender'])){
                $model->gender = $data['gender'];    
            }
            if(isset($data['government_id'])){
                $model->government_id = $data['government_id'];    
            }
            if(isset($data['government_id_exp'])){
                $model->government_id_exp = date('Y-m-d', strtotime($data['government_id_exp']));    
            }
            if(isset($data['country'])){
                $model->country = $data['country'];    
            }
            if(isset($data['state'])){
                $model->state = $data['state'];    
            }
            if(isset($data['city'])){
                $model->city = $data['city'];    
            }
            if(isset($data['postal_code'])){
                $model->postal_code = $data['postal_code'];    
            }
            if(isset($data['address'])){
                $model->address = $data['address'];    
            }
            $model->user->save();
            $model->save();

            $result['error'] = 0;
            $result['message'] = 'success';
            
        }

        return $result;

    }

    public function validate_request($request)
    {
        $input = [];

        $custom_error_msg = [];

        if (isset($request['email']))
        {
            $input['email'] = 'required|email';
            $custom_error_msg['email.required'] = 'Email is required.';
        }

        $validator = \Validator::make($request, $input, $custom_error_msg);

        return $validator;
    }
}
