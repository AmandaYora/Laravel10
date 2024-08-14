<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Services\Result;
use App\Services\Entities;
use App\Services\Person;
use App\Services\GetRequest;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $person;
    protected $getRequest;

    public function __construct()
    {
        $this->person = new Person();
        $this->getRequest = new GetRequest();
    }

    public function authLogin($identifier, $password)
    {
        return $this->person->login($identifier, $password);
    }

    public function authRegister(array $data)
    {
        return $this->person->register($data);
    }

    public function getCurrentUser($guid = null)
    {
        return $this->person->getCurrentUser($guid);
    }

    public function getUserAttributes($guid = null)
    {
        return $this->person->getUserAttributes($guid);
    }

    public function responseApi($data)
    {
        return response()->json($data);
    }

    public function getToken()
    {
        return Session::get('user_token');
    }

    public function saveUserAttr($guid = null)
    {
        return $this->person->saveUserAttr($guid);
    }
}
