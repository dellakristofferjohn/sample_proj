<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TblViews;

class VisitsTracker extends Controller
{
    public function visit(Request $request)
    {
        $params = $request->all();
        if(isset($params['model_id'])) {
            $model_id = $params['model_id'];    
        
            $visits = \App\Models\TblViews::where(array('model_id'=>$model_id))->get();

            foreach($visits as $views) {
                $track = \App\Models\TblViews::where('model_id',$model_id)->first();
                $view = json_decode($views->views);
                $view = $view + 1;
                $track->views = $view;
                $track->date_updated = gmdate('Y-m-d H:i:s');
                $track->save();
            }
        }

    	$status['message'] = 'success';

    	return $status;
    }
}
