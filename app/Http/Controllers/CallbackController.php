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
        $result = $request->all();
        if (isset($result['type']) && $result['type'] == 1) {
            Log::info('request1:'.var_export($request->all(),1));
            Log::info('file:'.var_export($_FILES, 1));
            //需要上传的文件
            $_FILE	= $_FILES['f']["tmp_name"];
            $post_data = array(
                'id' => 0,
                'type'=>1,
                'aucode' => "boqii",
                'subtype' => 'coupon',
                'method' => 'ajax',
                'upfile'=>"@".$_FILE,//绝对路径
            );
            $url =  "http://img.boqii.com/Server/upload.php";
            $result = $this->post_url($url,$post_data);
            Log::info('result_xx:'.var_export($result,1));
            return response($result);
        }
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
        $url = 'http://123.56.220.231';
        Log::info('data:'.var_export($data, 1));
        $downloadUrl = env('QINIU_DOMAINS_DEFAULT').'/'.$data['filename'];
        Log::info('downloadUrl:'.$downloadUrl);
        $fileinfo = explode('/',$data['filename']);
        Log::info('fileinfo:'.var_export($fileinfo, 1));
        $filePath = storage_path().'/'.$fileinfo[2];
        Log::info('filePath:'.$filePath);
        file_put_contents($filePath,'http://'.$downloadUrl);
        $ret = $this->upload_file($url,$data['filename']);
        //$ret = $this->upload_file($url,'http://'.$downloadUrl);
        Log::info('result:'.var_export($ret,1));
    }
    
    public function upload_file($url,$filename){
        $fileinfo = explode('/',$filename);
        $file = realpath(storage_path().'/'.$fileinfo[2]);
        $fields['f'] = new \CURLFile(realpath($file));
        $fields['type'] = 1;
        Log::info("realpath:".var_export($fields,1));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields );
        curl_exec( $ch );
        if ($error = curl_errno($ch)) {
            Log::info('errorsCode:'.var_export($error,1));
            Log::info('errorsInfo:'.var_export(curl_error($ch),1));
        }
        $return_data = curl_exec($ch);
        curl_close($ch);
        return $return_data;
    }

    public function post_url($url,$post_data,$time=100){
        $time = ($time<=30)?$time:30;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);        //CURLOPT_URL  需要获取的URL地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //CURLOPT_RETURNTRANSFER   将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_POST, 1);  //CURLOPT_POST  启用时会发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);   //CURLOPT_POSTFIELDS  全部数据使用HTTP协议中的"POST"操作来发送。要发送文件，在文件名前面加上@前缀并使用完整路径。这个参数可以通过urlencoded后的字符串类似'para1=val1&para2=val2&...'或使用一个以字段名为键值，字段数据为值的数组。如果value是一个数组，Content-Type头将会被设置成multipart/form-data。
        curl_setopt($ch, CURLOPT_TIMEOUT, $time);
        $output = curl_exec($ch);
        Log::info('curl_errno:'.curl_errno($ch));
        Log::info('curl_error:'.curl_error($ch));
        curl_close($ch);
        return $output;
    }
}
