<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
// use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Auth;


class ProtectedPageController extends BaseController
{

  public function __construct(){
  	if(Auth::check()){
    	if(session()->has('user')){
    		if (session()->get('user')['role_id'] == (config('application.role_id')['admin_id'] && config('application.role_id')['dev'])) {
           return redirect()->guest('admin/dashboard');
        }else{
    			return Redirect::to('panel/users');
        }
    	}
    }
    return view('auth/login', ['page_title' => 'Login']);
  }
}
