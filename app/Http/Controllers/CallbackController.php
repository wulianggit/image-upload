<?php
/**
 * Description :
 * User        : liang
 * Date        : 17/2/21 上午10:11
 * Author      : wuliang
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;

class CallbackController extends Controller
{
    public function callback(Request $request)
    {
        dd($request->all());
    }
}
