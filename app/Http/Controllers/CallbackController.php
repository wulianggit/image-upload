<?php
/**
 * Description :
 * User        : liang
 * Date        : 17/2/21 上午10:11
 * Author      : wuliang
 */

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    public function callback(Request $request)
    {
        $notifyBody = file_get_contents('php://input');
        Log::info('test:'.'this is test!');
        Log::info('request:'.print_r($request->all(),1));
        Log::info('notify:'.$notifyBody);
        return 1;
    }
}
