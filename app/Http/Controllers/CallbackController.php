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
use Qiniu\Storage\UploadManager;

class CallbackController extends Controller
{
    public function callback(Request $request)
    {
        // 用于签名的公钥和私钥
        $accessKey = env('QINIU_AXXESS_KEY');
        $secretKey = env('QINIU_SECRET_KEY');
        // 初始化签权对象
        $auth = new Auth($accessKey, $secretKey);
        $callbackBody = file_get_contents('php://input');//获取回调的body信息
        $contentType = 'application/x-www-form-urlencoded';
        //获取http头部的authorization 这里不同的服务器采用不同的方法来获取http头部
        if (strstr($request->server('SERVER_SOFTWARE'),"Apache")) {
            $data=apache_request_headers();
            $authorization = $data['Authorization'];
        } else {
            $authorization = $request->server('HTTP_AUTHORIZATION');
        }
        $url = 'http://123.56.220.231';
        $isQiniuCallback = $auth->verifyCallback($contentType, $authorization, $url, $callbackBody);
        if ($isQiniuCallback) {
            $this->handleData($request->all());
            $resp = array('ret' => 'this is test');
        } else {
            $resp = array('ret' => 'failed');
        }
        return response()->json($resp);
    }

    public function handleData($data)
    {
        $url = 'http://123.56.220.231/moveImage';
        Log::info('data:'.var_export($data, 1));
        $downloadUrl = env('QINIU_DOMAINS_DEFAULT').'/'.$data['filename'];
        Log::info('downloadUrl:'.$downloadUrl);
        $filePath = storage_path().$data['filename'];
        Log::info('filePath:'.$filePath);
        file_put_contents($filePath,'http://'.$downloadUrl);
        $ret = $this->upload_file($url,$data['filename']);
        Log::info('result:'.var_export($ret,1));
    }
    
    public function upload_file($url,$filename){
        $fields['f'] = '@'.storage_path().$filename;
        Log::info("fields['f']:".var_export($fields['f'],1));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields );
        curl_exec( $ch );
        if ($error = curl_error($ch) ) {
              Log::info('errors:'.$error);
        }
        $return_data = curl_exec($ch);
        curl_close($ch);
        echo $return_data; 
    }
}
