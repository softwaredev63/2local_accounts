<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserExtraWallet;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

class UserController extends Controller
{
    public function __construct()
    {
    }

    public function register(Request $request)
    {
		try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required'
            ], [
                'email.unique' => 'Email Already Exists',
            ]);

            if ($validator->fails()) throw new Exception($validator->errors());

            // Create new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            //Send verification email
            // $user->sendEmailVerificationNotification();
            
            $record = $user->toArray();
            $record['status'] = 0;
            $record['mobile_verification_status'] = 'No';

            return Response::json(array(
                'code' => 200,
                'message' => 'Congratulation!, You are registered successfully. We have sent you an email for account verification',
                'record' => $record
            ));
        } catch (Exception $exception) {
            return Response::json(array(
                'code' => 0,
                'message' => $exception->getMessage()
            ));
        }
    }
    
    /**
     * Login API for mobile app
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);
            if ($validator->fails()) throw new Exception($validator->errors());
            
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) throw new Exception('Invalid email or password');

            if ($user->email_verified_at == null) throw new Exception('First verify the account by click on the link in the Email');

            $user->api_token = Str::random(60);
            $user->save();

            $record = $this->populateExtraInfo($user);

            return Response::json(array(
                'code' => 200,
                'record' => $record,
            ));
        } catch (Exception $exception) {
            return Response::json(array(
                'code' => 1,
                'message' => $exception->getMessage()
            ));
        }
    }
    

    public function updateProfile(Request $request)
    {
        try {   
            $user = $request->user();

            if ($request->password) $user->password = Hash::make($request->password);
            if ($request->name) $user->name = $request->name;            
            $user->save();

            $profile = $user->profile;
            if (!$profile) $profile = new UserProfile;

            if ($request->first_name) $profile->first_name = $request->first_name;
            if ($request->last_name) $profile->last_name = $request->last_name;
            if ($request->birthday) $profile->birthday = $request->birthday;
            if ($request->country) $profile->country = $request->country;
            if ($request->city) $profile->city = $request->city;
            if ($request->state) $profile->state = $request->state;
            if ($request->post_code) $profile->post_code = $request->post_code;
            if ($request->address) $profile->address = $request->address;
            if ($request->mobile_number && $profile->mobile_number != $request->mobile_number) {
                $profile->mobile_number = $request->mobile_number;
                $profile->mobile_verified_at = null;
            }
            
            $user->profile()->save($profile);

            $record = $this->populateExtraInfo($user);
            return Response::json(array(
                'code' => 200,
                'message' => 'Account Settings Updated Successfully',
                'record' => $record,
            ));
        } catch (Exception $exception) {
            return Response::json(array(
                'code' => 1,
                'message' => $exception->getMessage()
            ));

        }
    }

    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();

            $record = $this->populateExtraInfo($user);

            return Response::json(array(
                'code' => 200,
                'message' => 'Get Profile Data Successfully',
                'record' => $record,
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'code' => 1,
                'message' => $e->getMessage()
            ));

        }
    }


    protected function populateExtraInfo($user) {
        $record = array(
            "id" => $user->id,
            "name" => $user->name,
            "user_type" => $user->user_type,
            "email" => $user->email,
            "twofa_status" => $user->two_factor_secret ? true : false,
            "api_token" => $user->api_token,
            "affiliate_code" => $user->affiliate_code,
            "affilated_status" => $user->affiliate_code ? 1 : 0,
            "locked" => $user->locked,
            "status" => $user->email_verified_at ? true : false,
            "access_token" => $user->api_token,
            "token_type" => "Bearer"
        );

        $profile = $user->profile;
        if ($profile) {
            $record['mobile_number'] = $profile->mobile_number;
            $record['mobile_verification_status'] = $profile->mobile_verified_at ? "Yes" : "No";
            $record['first_name'] = $profile->first_name;
            $record['last_name'] = $profile->last_name;
            $record['country'] = $profile->country;
            $record['country_code'] = $profile->country_code;
            $record['city'] = $profile->city;
            $record['state'] = $profile->state;
            $record['address'] = $profile->address;
            $record['image'] = $profile->image;
            $record['business_name'] = $profile->business_name;
            $record['website'] = $profile->website;
            $record['notes'] = $profile->notes;
            $record['hope'] = $profile->hope;
        }

        $wallet = $user->wallet;
        if ($wallet) {
            $record['wallet'] = $wallet->address;     
            $record['balance_bnb'] = floatval($wallet->balance_bnb);   
            $record['balance_2lc'] = floatval($wallet->balance_2lc);   
            $record['balance_locked_2lc'] = floatval($wallet->balance_locked_2lc);                        
        }

        return $record;
    }

    public function validateTwofaCode(Request $request)
    {
        
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required',
            ]);
            if ($validator->fails()) throw new Exception($validator->errors());

            $user = $request->user();
            if (!$user->two_factor_secret) throw new Exception('2FA not enabled');

            $code = $request->code;

            $valid = app(TwoFactorAuthenticationProvider::class)->verify(
                decrypt($user->two_factor_secret), $code
            );
                
            return Response::json(array(
                'code' => 200,
                'record' => ['valid' => $valid],
            ));
        } catch (Exception $exception) {
            return Response::json(array(
                'code' => 1, 
                'message' => $exception->getMessage()
            ));
        }
    }

    /**
     * Import old users from CSV file and update database
     */
    public function importOldUsers(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        try {
            $validator = Validator::make($request->all(), [
                'csv' => 'required',
            ]);
            if ($validator->fails()) throw new Exception($validator->errors());

            $file = fopen($request->file('csv')->getRealPath(), 'r');

            $cnt = 0;
            while (($item = fgetcsv($file, 0, "\t")) !== FALSE) {
                if ($cnt++ == 0) continue; // Avoid first row because it is column header

                $userData = array();

                $userData['name'] = $item[0];
                $userData['xlm_private_key'] = $item[1];
                $userData['xlm_public_key'] = $item[2];
                $userData['user_type'] = $item[3];
                $userData['email'] = $item[4];
                $userData['password'] = $item[5];
                $userData['mobile_number'] = $item[6];
                $userData['image'] = $item[7];
                $userData['business_name'] = $item[8];
                $userData['website'] = $item[9];
                $userData['notes'] = $item[10];
                $userData['first_name'] = $item[11];
                $userData['last_name'] = $item[12];
                $userData['birthday'] = $item[13];
                $userData['country'] = $item[14];
                $userData['city'] = $item[15];
                $userData['state'] = $item[16];
                $userData['post_code'] = $item[17];
                $userData['address'] = $item[18];
                $userData['remember_token'] = $item[19];
                $userData['twofa_secret'] = $item[20];
                $userData['status'] = $item[21];
                $userData['country_code'] = $item[22];
                $userData['hope'] = $item[23];
                $userData['btc_private_key'] = $item[24];
                $userData['btc_public_key'] = $item[25];
                $userData['eth_private_key'] = $item[26];
                $userData['eth_public_key'] = $item[27];
                $userData['api_token'] = $item[28];
                $userData['locked'] = $item[29];
                $userData['balance_l2l'] = $item[30];
                $userData['balance_xlm'] = $item[31];

                if (!$userData['email']) continue;

                // 1. Update/Create user
                $user = User::where('email', $userData['email'])->first();

                if ($user) {
                    $user->user_type = $userData['user_type'];
                } else {
                    $user = new User([
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'email_verified_at' => $userData['status'] == 'true' ? date('Y-m-d H:i:s') : null,
                        'password' => $userData['password'],
                        'remember_token' => $userData['remember_token'] ? $userData['remember_token'] : null,
                        'api_token' => $userData['api_token'] ? $userData['api_token'] : null,
                        'user_type' => $userData['user_type'],
                        'locked' => $userData['locked'],
                        'two_factor_secret' => $userData['twofa_secret'] ? encrypt($userData['twofa_secret']) : null
                    ]);
                }
                $user->save();

                // 2. Update/Create user profile
                $profile = $user->profile;
                if (!$profile) $profile = new UserProfile;

                $profile->first_name = $userData['first_name'];
                $profile->last_name = $userData['last_name'];
                $profile->country = $userData['country'];
                $profile->country_code = $userData['country_code'];
                $profile->city = $userData['city'];
                $profile->state = $userData['state'];
                $profile->post_code = $userData['post_code'];
                $profile->address = $userData['address'];
                $profile->mobile_number = $userData['mobile_number'];
                $profile->image = $userData['image'];
                $profile->business_name = $userData['business_name'];
                $profile->website = $userData['website'];
                $profile->notes = $userData['notes'];
                $profile->hope = $userData['hope'];

                $user->profile()->save($profile);

                // 3. Update/Create user extra wallet
                $extraWallet = $user->extraWallet;
                if (!$extraWallet) $extraWallet = new UserExtraWallet;

                $extraWallet->xlm_private_key = $userData['xlm_private_key'];
                $extraWallet->xlm_public_key = $userData['xlm_public_key'];
                $extraWallet->btc_private_key = $userData['btc_private_key'];
                $extraWallet->btc_public_key = $userData['btc_public_key'];
                $extraWallet->eth_private_key = $userData['eth_private_key'];
                $extraWallet->eth_public_key = $userData['eth_public_key'];
                $extraWallet->balance_l2l = $userData['balance_l2l'] ? (float)$userData['balance_l2l'] : 0;
                $extraWallet->balance_xlm = $userData['balance_xlm'] ? (float)$userData['balance_xlm'] : 0;

                $user->extraWallet()->save($extraWallet);
            }
            fclose($file);

            return Response::json(array(
                'success' => true,
                'message' => 'Updated successfully'
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'success' => false,
                'message' => $e->getMessage(),
            ), 400);
        }
    }
}
