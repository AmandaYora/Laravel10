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
        $result = new Result();

        if (!isset($data['code']) || !isset($data['password'])) {
            $result->info = "code and password is required!";
            return $this->responseApi($result);
        }

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

    public function saveUserAttribute()
    {
        Entities::Publish();
        $result = new Result();

        $guid = $this->getRequest->guid;
        $data = $this->getRequest->data;

        if (!isset($data)) {
            $result->code = Result::CODE_ERROR;
            $result->info = "data is required!";
            return $this->responseApi($result);
        }

        $mode = null;
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $mode = Person::MODE_ID;
        }

        $saveAttr = $this->saveUserAttr($guid, $data, $mode);
        
        $user = $this->getCurrentUser($guid);
        $userId = $user->code == Result::CODE_SUCCESS ? $user->data->user_id : null;

        
        if ($saveAttr) {
            $result->code = Result::CODE_SUCCESS;
            $result->info = "Attribute Berhasil Di tambahkan untuk user_id $userId";
        }else{
            $result->code = Result::CODE_ERROR;
            $result->info = "Attribute Gagal Di tambahkan untuk user_id $userId";
        }

        if (is_object($saveAttr)) {
            return $this->responseApi($saveAttr);
        }
        
        return $this->responseApi($result);
    }

}
