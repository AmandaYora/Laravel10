<?php

namespace App\Services;

class Result
{
    public $guid;
    public $code;
    public $info;
    public $data;
    public $message;

    // Constants for standardized codes
    const CODE_SUCCESS = 0;
    const CODE_ERROR = 1;

    /**
     * Create a new Result instance with default values.
     */
    public function __construct()
    {
        $this->guid = 0;
        $this->code = self::CODE_SUCCESS; // Default code indicating success
        $this->info = 'success'; // Default info indicating success
        $this->data = null;
    }

    /**
     * Convert the result instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'guid' => $this->guid,
            'code' => $this->code,
            'info' => $this->info,
            'data' => $this->data,
        ];
    }
}
