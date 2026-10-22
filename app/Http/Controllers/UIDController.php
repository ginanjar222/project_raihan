<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class UIDController extends Controller
{
    public function setUID(Request $request)
    {
        $uid = $request->input('uid');
        File::put(public_path('temp_uid.txt'), $uid);
        return response()->json(['status' => 'UID updated']);
    }

    public function getUID()
    {
        $tempFiles = [
            public_path('temp_uid.txt'),
            storage_path('app/temp_uid.txt')
        ];
        
        foreach ($tempFiles as $filePath) {
            if (File::exists($filePath)) {
                $uid = trim(File::get($filePath));
                if (!empty($uid)) {
                    return response()->json([
                        'status' => 'success',
                        'uid' => $uid,
                        'timestamp' => filemtime($filePath)
                    ]);
                }
            }
        }
        
        return response()->json([
            'status' => 'no_uid',
            'uid' => null
        ]);
    }
}
