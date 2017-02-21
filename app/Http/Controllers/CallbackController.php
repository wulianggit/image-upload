<?php
/**
 * Description :
 * User        : liang
 * Date        : 17/2/21 上午10:11
 * Author      : wuliang
 */

namespace App\Http\Controllers;

use Log;
use Qiniu\Auth;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    public function callback(Request $request)
    {
        Log::info('server:'.print_r($_SERVER,1));
        Log::info('request-header:'.print_r($request->header(),1));
        Log::info('request-server:'.print_r($request->server(),1));
        return 1;
        // 用于签名的公钥和私钥
        $accessKey = env('QINIU_AXXESS_KEY');
        $secretKey = env('QINIU_SECRET_KEY');
        // 初始化签权对象
        $auth = new Auth($accessKey, $secretKey);
        $callbackBody = file_get_contents('php://input');//获取回调的body信息
        $contentType = 'application/x-www-form-urlencoded';
        //获取http头部的authorization 这里不同的服务器采用不同的方法来获取http头部
        if (strstr($_SERVER["SERVER_SOFTWARE"],"Apache")) {
            $data=apache_request_headers();
            $authorization = $data['Authorization'];
        } else {
            $authorization = $_SERVER['HTTP_AUTHORIZATION'];
        }
        $url = 'http://123.56.220.231';
        $isQiniuCallback = $auth->verifyCallback($contentType, $authorization, $url, $callbackBody);
        if ($isQiniuCallback) {
            $resp = array('ret' => 'success');
        } else {
            $resp = array('ret' => 'failed');
        }
        return 1;
    }
}
