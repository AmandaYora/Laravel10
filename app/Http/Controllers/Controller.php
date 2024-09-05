<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Services\Result;
use App\Services\Entities;
use App\Services\Person;
use App\Services\GetRequest;
use Illuminate\Support\Facades\Session;

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

    public function authRegister($data)
    {
        return $this->person->register($data);
    }

    public function getCurrentUser($guid = null)
    {
        if (is_null($guid)) {
            $guid = $this->getToken() ?? $this->getRequest->guid;
        }

        return $this->person->getCurrentUser($guid) ?: false;
    }

    public function getAllUsers($mode = null)
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser->code == Result::CODE_SUCCESS) {
            $users = $this->person->getAllUsers($mode);

            return $users;
        }
        return $currentUser;
    }


    public function getUserAttributes($guid = null)
    {
        if (is_null($guid)) {
            $guid = $this->getToken() ?? $this->getRequest->guid;
        }

        return $this->person->getUserAttributes($guid);
    }

    public function responseApi($data)
    {
        return response()->json($data);
    }

    public function getToken()
    {
        return (Session::get('user_token') ? Session::get('user_token') : session('user_token')) ?? null;
    }

    public function saveUserAttr($data, $mode = Person::MODE_TOKEN, $guid = null)
    {
        if (is_null($guid)) {
            $guid = $this->getToken() ?? $this->getRequest->guid;
        }

        return $this->person->saveUserAttr($guid, $data, $mode);
    }
}
