<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TblAffiliateTracker;

class AffiliateTracker extends Controller
{
    public function track(Request $request)
    {
    	$tracker = new TblAffiliateTracker();
    	$tracker->status = true;
    	$tracker->date_created = gmdate('Y-m-d H:i:s');
    	$tracker->save();

    	$status['message'] = 'success';

    	return $status;
    }
}
