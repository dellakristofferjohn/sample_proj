<?php

namespace App\Http\Controllers\Mock\Dev;

use Illuminate\Http\Request;
use App\Http\Controllers\ProtectedPageController;
use App\Models\TblRoles;


class Permissions extends ProtectedPageController
{
	public function index(){
		return view('mock/dev/permissions', ["page_title" => "Permissions"]);
	}

    public function overview(){
	    $permissions = config("permissions.permissions");

	    $roles =  \DB::table('tbl_roles')->get()->toArray();
	    if($roles == null) die("no groups");

	    return view('mock/dev/permissionsOverview', ["groups" => $roles, "permissions" => $permissions, "page_title" => "Permissions Overview"]);
	}

	public function process_overview(Request $request){
		$group = TblRoles::find($request->input("group_id"));
		$permission = $request->input("permission");
		$permission_name = str_replace("_", " ", $permission);

		if($group && !empty($permission)){
			switch($request->input("set_flag")){
				case "red":
					// echo "giving permission...";
					self::add_permission($group, $permission);
					$log = "{$group->name}: [+] {$permission_name}";
					break;
				case "green":
					// echo "removing permission...";
					self::remove_permission($group, $permission);
					$log = "{$group->name}: [-] {$permission_name}";
					break;
			}
		}

		echo $log;
	}

	protected function add_permission(TblRoles $group, $permission){

		$permissions_array = json_decode($group->permissions, true);

	    $permissions_array[$permission] = 1;

	    $group->permissions = json_encode($permissions_array);
	    $group->save();
	}

	protected function remove_permission(TblRoles $group, $permission){
		$permissions_array = json_decode($group->permissions, true);
	    unset($permissions_array[$permission]);
	    
	    $group->permissions = json_encode($permissions_array);
	    $group->save();
	}
}
