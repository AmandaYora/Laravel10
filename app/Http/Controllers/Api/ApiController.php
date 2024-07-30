<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Result;
use App\Services\Entities;
use App\Services\Person;
use App\Services\Notification;
use Illuminate\Support\Facades\Session;

class ApiController extends Controller
{
    public function login()
    {
        Entities::Publish();

        $data = $this->getRequest->data;
        $login = $this->authLogin($data['code'], $data['password']);

        return $this->responseApi($login);
    }

    public function register()
    {
        Entities::Publish();

        $data = $this->getRequest->data;
        $register = $this->authRegister($data);

        return $this->responseApi($register);
    }

    public function getUser()
    {
        Entities::Publish();

        $guid = $this->getRequest->guid;
        $user = $this->getCurrentUser($guid);
        return $this->responseApi($user);
    }

    public function getAttribute()
    {
        Entities::Publish();

        $guid = $this->getRequest->guid;
        $user = $this->getUserAttributes($guid);
        return $this->responseApi($user);
    }

}
