<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ReturnResponse
{
    public function returnError($errorNum,$msg): JsonResponse
    {
        return response()->json([
            'status' => false,
            'msg' => $msg
        ], $errorNum);
    }


    public function returnSuccessMessage($msg): JsonResponse
    {
        return response()->json([
            'status' => true,
            'msg' => $msg
        ]);
    }

    public function returnData($key, $value, $msg=''): JsonResponse
    {
        return response()->json([
            'status' => true,
            'msg' => $msg,
            $key => $value
        ]);
    }

}
