<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rfid;

class RfidController extends Controller
{
    public function store(Request $request)
{
    $uid = $request->input('uid');
    file_put_contents(storage_path('app/temp_uid.txt'), $uid);
    return response()->json(['status' => 'received', 'uid' => $uid]);
}
}
