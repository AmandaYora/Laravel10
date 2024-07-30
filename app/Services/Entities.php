<?php

namespace App\Services;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class Entities
{
    public static function publish()
    {
        // Mengambil request dan response instance
        $request = Request::instance();
        $response = Response::make();

        $origin = $request->header('Origin');

        // Mengatur header CORS
        $response->header('Access-Control-Allow-Origin', $origin ? $origin : '*');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->header('Access-Control-Allow-Credentials', 'true');
        $response->header('Access-Control-Max-Age', '86400');

        // Tandai aplikasi sebagai CORS-published
        app()->instance('cors_published', true);

        if ($request->getMethod() === 'OPTIONS') {
            $response->setStatusCode(204);
            $response->setContent('');
            $response->send();
            exit;
        }

        // Kembalikan response agar dapat digunakan oleh controller
        return $response;
    }
}
