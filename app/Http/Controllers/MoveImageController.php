<?php
/**
 * Description :
 * User        : liang
 * Date        : 17/2/21 下午3:33
 * Author      : wuliang
 */

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;

class MoveImageController extends Controller
{
    public function moveImage(Request $request)
    {
        Log::info('request:'.var_export($request->all(),1));
        return response()->json(['status'=>'ok']);
    }
}
