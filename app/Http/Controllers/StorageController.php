<?php namespace App\Http\Controllers;

use zgldh\QiniuStorage\QiniuStorage;

class StorageController extends Controller
{
    public function upload()
    {
        $disk = \Storage::disk('qiniu');
        //dd($disk);
    }
}
