<?php
namespace App\Traits;


trait Response{

    public function success($data,  $message, $status = 200){
         return response()->json([
             "message" => $message,
             "data" => $data
         ], $status);

    }

    public function error($data,  $message, $status = 400){
        return response()->json([
            "message" => $message,
            "data" => $data
        ], $status);

    }
}

?>
