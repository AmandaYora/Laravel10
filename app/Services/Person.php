<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Role;
use App\Services\Attribute as AttributeService;


class Person extends Model
{
    const MODE_TOKEN = 1;
    CONST MODE_ID = 2;
    // Define a constant for the error message
    const USER_NOT_FOUND_MESSAGE = 'User not found or session expired';

    public function login($identifier, $password)
    {
        $result = new Result();

        try {
            $user = User::with('roles.role')
                        ->where('email', $identifier)
                        ->orWhere('code', $identifier)
                        ->first();

            if ($user && Hash::check($password, $user->password)) {

                if($user->is_verify == "false" || $user->is_verify == "0"){
                    $result->code = Result::CODE_ERROR;
                    $result->info = 'failed';
                    $result->message = 'Your account has not been verified yet.';
                    return $result;
                }

                $uuid = Str::uuid()->toString();
                $user->token = str_replace('-', '', $uuid);
                $save = $user->save();

                if (!$save) {
                    $result->code = Result::CODE_ERROR;
                    $result->info = 'failed';
                    $result->message = 'Failed to update credential token';
                    return $result;
                }

                Session::put('user_token', $user->token);
                session(['user_token' => $user->token]);

                $result->code = Result::CODE_SUCCESS;
                unset($user['password']);
                unset($user['roles']);

                $roles = null;
                $mappedRoles = $user->getMappedRoles();
                if (!empty($mappedRoles)) {
                    $roles = $mappedRoles;
                }

                $user['roles'] = $roles;

                $result->data = $user;
                return $result;
            }

            $result->code = Result::CODE_ERROR;
            $result->info = 'failed';
            $result->message = 'Invalid credentials';
        } catch (\Exception $e) {
            $result->code = Result::CODE_ERROR;
            $result->info = 'failed';
            $result->message = $e->getMessage();
        }

        return $result;
    }

    public function register($data)
    {
        $result = new Result();

        try {
            if (!isset($data['code']) || !isset($data['name']) || !isset($data['phone']) || !isset($data['email']) || !isset($data['password'])) {
                $missingFields = [];
            
                if (!isset($data['code'])) {
                    $missingFields[] = 'code';
                }
                if (!isset($data['name'])) {
                    $missingFields[] = 'name';
                }
                if (!isset($data['phone'])) {
                    $missingFields[] = 'phone';
                }
                if (!isset($data['email'])) {
                    $missingFields[] = 'email';
                }
                if (!isset($data['password'])) {
                    $missingFields[] = 'password';
                }
            
                $result->code = Result::CODE_ERROR;
                $result->info = 'failed';
                $result->message = 'Required fields missing: ' . implode(', ', $missingFields);
                return $result;
            }

            $existingUser = User::where('email', $data['email'])
                                ->orWhere('code', $data['code'])
                                ->first();

            if ($existingUser) {
                $result->code = Result::CODE_ERROR;
                $result->info = 'failed';
                $result->message = 'User already exists';
                return $result;
            }

            $user = new User();
            $user->code = $data['code'];
            $user->name = $data['name'];
            $user->phone = $data['phone'];
            $user->email = $data['email'];
            $user->password = $data['password'];
            $user->token = str_replace('-', '', Str::uuid()->toString());
            $user->is_verify = 'false';

            $save = $user->save();

            if (!$save) {
                $result->code = Result::CODE_ERROR;
                $result->info = 'failed';
                $result->message = 'Failed to register user';
                return $result;
            }

            $result->code = Result::CODE_SUCCESS;
            unset($user['password']);
            unset($user['token']);
            $result->message = "Your request has been submitted and is pending admin verification.";
            $result->data = $user;
        } catch (\Exception $e) {
            $result->code = Result::CODE_ERROR;
            $result->info = 'failed';
            $result->message = $e->getMessage();
        }

        return $result;
    }

    public function getCurrentUser($guid = null)
    {
        $result = new Result();
        $result->code = Result::CODE_ERROR;
        $result->info = 'failed';
        $result->message = self::USER_NOT_FOUND_MESSAGE;

        try {
            $query = User::with(['roles.role']);
        
            if ($guid) {
                $query->where('token', $guid);
            } else {
                $token = Session::get('user_token');
                if ($token) {
                    $query->where('token', $token);
                } else {
                    $result->code = Result::CODE_ERROR;
                    $result->info = 'failed';
                    $result->message = self::USER_NOT_FOUND_MESSAGE;
                    return $result;
                }
            }
        
            $user = $query->first();
        
            if ($user) {
                $result->code = Result::CODE_SUCCESS;
                unset($user['password']);
                unset($user['role_id']);
                unset($user['token']);
                unset($user['roles']);
                
                $mappedAttributes = $user->getMappedAttributes();
                if (!empty($mappedAttributes)) {
                    $user['attribute'] = $mappedAttributes;
                }

                $roles = null;
                $mappedRoles = $user->getMappedRoles();
                if (!empty($mappedRoles)) {
                    $roles = $mappedRoles;
                }

                $user['roles'] = $roles;
                
                $result->data = $user;
                
                return $result;
            }

        } catch (\Exception $e) {
            $result->info = "failed get data : $e";
            return $result;
        }

        $result->info = "failed get data";
        return $result;
    }

    public function getAllUsers($mode = self::MODE_TOKEN)
    {
        $result = new Result();

        try {
            $users = User::with(['roles.role'])->get();

            if ($users->isEmpty()) {
                $result->code = Result::CODE_ERROR;
                $result->info = 'failed';
                $result->message = self::USER_NOT_FOUND_MESSAGE;
                return $result;
            }

            $usersData = [];
            foreach ($users as $user) {
                unset($user['password']);
                unset($user['role_id']);
                if ($mode !== self::MODE_ID) {
                    unset($user['token']);
                }

                $mappedAttributes = $user->getMappedAttributes();
                if (!empty($mappedAttributes)) {
                    $user['attribute'] = $mappedAttributes;
                }

                $roles = null;
                $mappedRoles = $user->getMappedRoles();
                if (!empty($mappedRoles)) {
                    $roles = $mappedRoles;
                }

                $user['roles'] = $roles;
                $usersData[] = $user;
            }

            $result->code = Result::CODE_SUCCESS;
            $result->data = $usersData;

            return $result;

        } catch (\Exception $e) {
            $result->code = Result::CODE_ERROR;
            $result->info = 'failed';
            $result->message = $e->getMessage();
            return $result;
        }
    }

    public function getUserAttributes($guid = null)
    {
        $userResult = $this->getCurrentUser($guid);

        if ($userResult->code === Result::CODE_SUCCESS) {
            $user = $userResult->data;
            $userId = $user->user_id;
            $code = $user->code;

            // Instansiasi layanan Attribute secara langsung
            $attributeService = new AttributeService(new \App\Models\Attribute());
            $attributes = $attributeService->getMappedAttributesByUserId($userId);

            $user = [];
            $user['user_id'] = $userId;
            $user['code'] = $code;
            $user['attributes'] = $attributes;
            $userResult->data = $user;
            return $userResult;
        }
        
        $userResult->info = 'failed';
        return $userResult;
    }

    public function saveUserAttr($guid, $data, $mode = self::MODE_TOKEN){
        $user = $this->getCurrentUser($guid);

        if ($user->code != Result::CODE_SUCCESS) {
            return $user;
        }

        if($mode == self::MODE_TOKEN){
            $user_id = $user->data->user_id;
        }elseif($mode == self::MODE_ID){
            $user_id = $data['user_id'];
        }
        
        $attributeService = new AttributeService(new \App\Models\Attribute());
        $saveAttr = $attributeService->saveAttr($user_id, $data, $mode);

        return $saveAttr;
    }

}
