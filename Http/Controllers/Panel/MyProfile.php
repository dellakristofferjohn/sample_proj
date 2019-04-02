<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TblUsers;
use App\Models\TblImages;
use App\Models\TblModels;
use App\Models\TblPackages;
use App\Helpers\UtilHelper;

class MyProfile extends Controller {

	public function index(){
		$data = [];

		$id = session()->get('user')['id'];
		$data['user'] = TblUsers::find($id)->toArray();

		return view('panel/myProfile',$data); 
	}

	public function sub_offer(){
		$package = TblPackages::where('model_id',session()->get('user')['model_id'])
								->orWhere('model_id',0)->get()->toArray();
		die(json_encode($package));
	}

	protected function validate_user($data)
	{

		$input = [
			'first_name' => 'required|min:2|max:25',
			'last_name' => 'required|min:2|max:25',
			'email' => 'required|email|min:3|max:255'
		];


		$message = [
			'first_name.required' => 'Please Enter Firstname',
			'last_name.required' => 'Please Enter Lastname',
			'email.required' => 'Please Enter Email'
		];

		if(isset($data['password']) && trim($data['password']) != ''){
			$input['password'] = 'required|min:3|max:25';
			$message['password.required'] = 'Please Enter Password';
		}

		if(isset($data['phone_number']) && !is_null($data['phone_number'])){
			$input['phone_number'] = 'required|numeric|min:3';
			$message['phone_number.required'] = 'Please Enter Phone Number';
		}
		
		$validator = \Validator::make($data, $input, $message);

		return $validator;
	}

	public function save_profile(Request $request)
	{
		$data = $request->all();
		$user_data = [];
		$result = [];
		$result['error'] = 0;
		$result['message'] = [];

		$val = $this->validate_user($data);

		if($val->fails()){
			$result['error'] = 1;
			$result['message'] = $val->errors();
		} else {

			//return json_encode($data);
			if(isset($data['first_name'])){
				$user_data['first_name'] = $data['first_name'];
			}

			if(isset($data['last_name'])){
				$user_data['last_name'] = $data['last_name'];
			}

			if(isset($data['email'])){
				$email_check = TblUsers::where(['email' => $data['email'], 'id' => session()->get('user')['id']])->first();

				$email_check2 = TblUsers::where(['email' => $data['email']])->first();

				if(($email_check && $email_check2) || (!$email_check && !$email_check2)){
					$user_data['email'] = $data['email'];
				} else {
					$result['error'] = 1;
					$result['message']['email'][0] = "Email Has Already been Taken"; 
				}
			}

			if(isset($data['gender'])){
				$user_data['gender'] = $data['gender'];
			}

			if(isset($data['phone_number'])){
				$user_data['phone_number'] = $data['phone_number'];
			}

			if((isset($data['password']) && !preg_match('/\s/',$data['password'])) && (isset($data['confpassword']) && !preg_match('/\s/',$data['confpassword']))){
				$password = trim($data['password']);
				$confpassword = trim($data['confpassword']);
				if($password == $confpassword){
					$user_data['password'] = bcrypt($password);
				} else {
					$result['error'] = 1;
					$result['message']['password'][0] = "Password Does Not Match";
				}
			} else {
				$result['error'] = 1;
				$result['message']['password'][0] = "Password Has Spaces";
			}

			if($result['error'] == 0){
				$result['message'] = "Success Model Has Been Updated";
				TblUsers::where('id', session()->get('user')['id'])->update($user_data);
				$user = TblUsers::where(['id' => session()->get('user')['id']])->first();

				$user_session = [
					'id'         => $user->id,
					'username'   => $user->email,
					'email'      => $user->email,
					'role_id'    => $user->role_id,
					'firstname'  => $user->first_name,
					'lastname'	  => $user->last_name
				];

				session(['user' => $user_session]);
			}

		}

		return $result;
	}
}
