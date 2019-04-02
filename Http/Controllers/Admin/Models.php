<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ProtectedPageController;
use App\Models\TblUsers;
use App\Models\TblModels;
use App\Models\TblPackages;
use App\Models\TblDefaultPackages;
use App\Models\TblImages;
use App\Helpers\UtilHelper;

class Models extends ProtectedPageController {
	public function index(Request $request){
		$data = [];
		$data['start_date'] = date('Y-m-01 00:00:00');
		$data['end_date'] = date('Y-m-t 23:59:59');


		if ($request->isMethod('post')) {
			$params = $request->all();
			\Session::put('models_from_date', $params['from']);
			\Session::put('models_to_date', $params['to']);
			$data['models'] = TblModels::get_models($params)->toArray();
		} else {
			$data['models'] = TblModels::get_models()->toArray();
		}
		return view('admin/panel/models',$data); 
	}

	protected function validate_model($data)
	{

		$input = [
			'firstname' => 'required|min:2|max:25',
			'lastname' => 'required|min:2|max:25',
			'email' => 'required|email|min:3|max:255',
			'profName' => 'required|min:3|max:25',
			'privateSnapChat' => 'required|min:2|max:25',
			'password' => 'required|min:3|max:25'
		];

		$message = [
			'firstname.required' => 'Please Enter Firstname',
			'lastname.required' => 'Please Enter Lastname',
			'email.required' => 'Please Enter Email',
			'profName.required' => 'Please Enter Profile Name',
			'privateSnapChat.required' => 'Please Enter SnapChat Name',
			'password.required' => 'Please Enter Password'
		];

		$validator = \Validator::make($data, $input, $message);

		return $validator;
	}

	public function get_model_info(Request $request){
		$data = $request->all();

		$results = TblModels::get_model_info($data);

		return json_encode($results); 
	}

	public function addOrUpdateModel(Request $request)
	{
		$result = array();
		$data = $request->all();
		$val = $this->validate_model($data);

		if($val->fails()){
			$result['error'] = 1;
			$result['message'] = $val->errors();
		} else {
			if ( !preg_match('/\s/',$data['profName']) && !preg_match('/\s/', $data['privateSnapChat'])) {
				if(is_null($data['user_id'])){
					$result = $this->add_model($data);
				} else {
					$result = $this->edit_model($data);
				}
			} else {
				$result['error'] = 1;
				$result['message'] = array();

				if(preg_match('/\s/',$data['profName']))
					$result['message']['profName'][0] = 'Oops! Looks Like Your Profile Name Has Spaces You Can Replace It With "_"';
					
				if(preg_match('/\s/', $data['privateSnapChat']))
					$result['message']['privateSnapChat'][0] = 'Oops! Looks Like Your Snapchat ID Has Spaces You Can Replace It With "_"';

			}
		}
		return $result;
	}

	public function edit_model($data)
	{
		$result = [];
		$result['error'] = 0;
		$result['message'] = [];

		$user_data = [];
		$model_data = [];

		$model = TblModels::where(array('user_id' => $data['user_id']))->first();

		if(isset($data['client_sub_account'])){
			$model->client_sub_account = $data['client_sub_account'];			
			$model->save();
		}

		if(isset($data['split_option'])){
			$model->split_option = $data['split_option'];			
			$model->save();
		}

		if(isset($data['lastname']))
			$user_data['last_name'] = $data['lastname'];

		if(isset($data['firstname']))
			$user_data['first_name'] = $data['firstname'];

		if(isset($data['email'])){
			$email_check = TblUsers::where(['email' => $data['email'], 'id' => $data['user_id']])->first();
			$email_check2 = TblUsers::where(['email' => $data['email']])->first();

			if(($email_check && $email_check2) || (!$email_check && !$email_check2)){
				$user_data['email'] = $data['email'];
			} else {
				$result['error'] = 1;
				$result['message']['email'][0] = "Email Has Already been Taken"; 
			}
		}

		if(isset($data['password']) && $data['password'] != 'no change')
			$user_data['password'] = bcrypt($data['password']);

		if(isset($data['profName'])){
			$profile_name_check = TblModels::where(['profile_name' => $data['profName'], 'user_id' => $data['user_id']])->first();
			$profile_name_check2 = TblModels::where(['profile_name' => $data['profName'] ])->first();

			if(($profile_name_check && $profile_name_check2) || (!$profile_name_check && !$profile_name_check2)){
				$model_data['profile_name'] = $data['profName'];
			} else {
				$result['error'] = 1;
				$result['message']['profName'][0] = "Profile Name Has Already been Taken";
			}
			
		}

		if(isset($data['privateSnapChat'])){
			$snapchat_id_check = TblModels::where(['snapchat_id' => $data['privateSnapChat'], 'user_id' => $data['user_id']])->first();
			$snapchat_id_check2 = TblModels::where(['snapchat_id' => $data['privateSnapChat'], 'user_id' => $data['user_id']])->first();

			if(($snapchat_id_check && $snapchat_id_check2) || (!$snapchat_id_check && !$snapchat_id_check2)){
				$model_data['snapchat_id'] = $data['privateSnapChat'];
			} else {
				$result['error'] = 1;
				$result['message']['privateSnapChat'][0] = "Snapchat ID Has Already been Taken";
			}
		}

		if($result['error'] == 0){
			$result['message'] = "Success Model Has Been Updated";
			TblUsers::where('id', $data['user_id'])->update($user_data);
			TblModels::where('user_id', $data['user_id'])->update($model_data);
		}

		return $result;
	}

	public function add_model($data)
	{
		$email = UtilHelper::sanitize($data['email'], 1);
		$firstname = UtilHelper::sanitize($data['firstname'], 1);
		$lastname = UtilHelper::sanitize($data['lastname'], 1);

		$email_check = TblUsers::where(['email' => $email])->first();
		$profile_name_check = TblModels::where(['profile_name' => $data['profName']])->first();
		$snapchat_id_check = TblModels::where(['snapchat_id' => $data['privateSnapChat']])->first();
		
		if ( !$email_check && !$profile_name_check && !$snapchat_id_check ) {
			$newUser = new TblUsers;
			$newUser->first_name = $firstname;
			$newUser->last_name = $lastname;
			$newUser->email = $email;
			$newUser->role_id = 2;
			$newUser->password = bcrypt($data['password']);
			$newUser->save();

			$profName = UtilHelper::sanitize($data['profName'], 1);
			$privateSnapChat = UtilHelper::sanitize($data['privateSnapChat'], 1);
			$profile = TblModels::firstOrNew(['user_id' => $newUser->id]);
			$profile->user_id = $newUser->id;
			$profile->profile_name = $profName;
			$profile->description = "Hey guys it's ".$profName.", do you want to have some fun?! Then add me on snapchat or instagram. Better be ready, it's gonna get dirty! ;)";
			$profile->snapchat_id = $privateSnapChat;
			$profile->date_created = gmdate('Y-m-d H:i:s');
			$profile->save();

			$userDetail = TblUsers::find($newUser->id);
            $userDetail->role_id = config('application.role_id')['model_id'];
            $userDetail->save();

			$default_packages = TblDefaultPackages::get();
			if (count($default_packages))
			{
				$defCount = 1;
				foreach ($default_packages as $p) {
					$p['model_id'] = $profile->id;
					$p['is_edit'] = false;
					if ($defCount == 1) 
						$p['defaultOpt'] = 1;
					
					$defCount++;
					TblPackages::add_package($p);
				}
			}

			$saveImage =  new TblImages;
			$saveImage->model_id = $profile->id;
			$saveImage->name = 'no-image.jpg';
			$saveImage->img_num = 1;
			$saveImage->status = 1;
			$saveImage->date_created = gmdate('Y-m-d H:i:s');
			$saveImage->save();

			$result['error'] = 0;
			$result['message'] = "Success Model Has Been Added";
		} else {
			$result['error'] = 1;
			$result['message'] = array();

			if($email_check)
				$result['message']['email'][0] = "Email Has Already been Taken"; 

			if($profile_name_check)
				$result['message']['profName'][0] = "Profile Name Has Already been Taken";

			if($snapchat_id_check)
				$result['message']['privateSnapChat'][0] = "Snapchat ID Has Already been Taken";
		}

		return $result;
	}
}