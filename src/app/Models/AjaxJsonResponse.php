<?php
namespace App\Models;

class AjaxJsonResponse {

    public static function make(string $message, array $errors = [], array $data = []) {
        return response()->json(['message' => $message, 'errors' => $errors, 'data' => $data]);
    }

}