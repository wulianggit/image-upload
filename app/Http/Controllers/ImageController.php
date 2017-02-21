<?php
/**
 * Description :
 * User        : liang
 * Date        : 17/2/20 下午4:59
 * Author      : wuliang
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class ImageController extends Controller
{
    public function testUpload(Request $request)
    {
        // 用于签名的公钥和私钥
        $accessKey = env('QINIU_AXXESS_KEY');
        $secretKey = env('QINIU_SECRET_KEY');
        // 初始化签权对象
        $auth = new Auth($accessKey, $secretKey);

        // 要上传文件的本地路径
        $filePath  = storage_path().'/images/php-logo.png';
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $filename  = 'Data/shop/'.md5(date('Y-m-d').uniqid()).'.'.$extension;
        $policy = [
            'saveKey'=>$filename,
            'callbackUrl' => 'http://123.56.220.231',
            'callbackBody' => 'filename='.$filename,
            'callbackBodyType'=>"application/x-www-form-urlencoded"
        ];

        // 要上传的空间
        $bucket = env('QINIU_BUCKET');
        // 生成上传 Token
        $token = $auth->uploadToken($bucket, null, 3600, $policy);
        //dd($token);

        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。

        list($ret, $err) = $uploadMgr->putFile($token, $filename, $filePath);

        if ($err !== null) {
            var_dump($err);
        } else {
            var_dump($ret);
        }

    }
}
