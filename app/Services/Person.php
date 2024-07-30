<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Role;


class Person extends Model
{
    // Define a constant for the error message
    const USER_NOT_FOUND_MESSAGE = 'User not found or session expired';

    public function login($identifier, $password)
    {
        $result = new Result();

        try {
            $user = User::with('role')
                        ->where('email', $identifier)
                        ->orWhere('code', $identifier)
                        ->first();

            if ($user && Hash::check($password, $user->password)) {
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

                $result->code = Result::CODE_SUCCESS;
                unset($user['password']);
                unset($user['role_id']);
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

    public function register(array $data)
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
            $user->role_id = Role::ROLE_USER;

            if (!$user->save()) {
                $result->code = Result::CODE_ERROR;
                $result->info = 'failed';
                $result->message = 'Failed to register user';
                return $result;
            }

            $user->load('role');
            $result->code = Result::CODE_SUCCESS;
            unset($user['password']);
            unset($user['token']);
            unset($user['role_id']);
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

        try {
            $query = User::with('role');
        
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
                if ($user->role_id == 2) {
                    $userData = $user->toArray();
                    $userData['stores'] = [];
    
                    foreach ($user->userMappingStores as $mappingStore) {
                        $store = $mappingStore->store->toArray();
                        $store['target_penjualan'] = [];
    
                        foreach ($mappingStore->store->targetMappingStores as $targetMappingStore) {
                            $targetPenjualan = $targetMappingStore->targetPenjualan->toArray();
                            $store['target_penjualan'][] = $targetPenjualan;
                        }
    
                        $userData['stores'][] = $store;
                    }
    
                    unset($userData['user_mapping_stores']);
                }
    
                $result->code = Result::CODE_SUCCESS;
                unset($userData['password']);
                unset($userData['role_id']);
                $result->data = $userData;
                return $result;
            }

            $result->code = Result::CODE_ERROR;
            $result->info = 'failed';
            $result->message = self::USER_NOT_FOUND_MESSAGE;
        } catch (\Exception $e) {
            $result->code = Result::CODE_ERROR;
            $result->info = 'failed';
            $result->message = $e->getMessage();
        }

        return $result;
    }

}
