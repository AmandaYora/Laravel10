<?php

namespace App\Services;

class GetRequest
{
    public $guid;
    public $code;
    public $info;
    public $data;

    public function __construct()
    {
        $this->guid = $this->getInput('guid');
        $this->code = $this->getInput('code');
        $this->info = $this->getInput('info');
        $this->data = $this->getInput('data');

        // Mengatur default menjadi array jika 'data' berupa string JSON
        if (is_string($this->data)) {
            $decodedData = json_decode($this->data, true); // Decode as array
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->data = $decodedData;
            }
        }
    }

    private function getInput($key)
    {
        // Cek di $_POST terlebih dahulu
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }

        // Jika tidak ada di $_POST, cek di $_GET
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }

        // Jika tidak ada di $_POST atau $_GET, cek input JSON di php://input
        $json = file_get_contents('php://input');
        $decoded = json_decode($json, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded[$key] ?? null;
        }

        return null;
    }

    public function toObject()
    {
        return $this->wrapDataToObject($this->data);
    }

    private function wrapDataToObject($data)
    {
        if (is_array($data)) {
            return json_decode(json_encode($data));
        }

        return $data;
    }
}
