<?php

namespace App\Http\Controllers\Reset;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;
use App\Services\UserWalletService;
use App\Services\SecretPhraseService;

use App\Models\User;
use App\Models\UserWallet;

class ResetAccountController extends Controller
{
  protected $userWalletService;
  protected $secretPhraseService;
  
  public function __construct()
  {
    
  }

  public function checkLoginStatus(Request $request)
  {
    if (!$request->email || !$request->password){
      return array("login_status" => false, 'message' => "Fill all fields for Email and Password", "user_id" => "");
    } else {
      $user = User::where('email', $request->email)->first();
      if(!$user)
        return array("login_status" => false, 'message' => "This email account is not existing!", "user_id" => "");
      else {
        if (!Hash::check($request->password, $user->password)) 
          return array("login_status" => false, 'message' => "Password doesn't match!", "user_id" => "");
        else {
          return array("login_status" => true, 'message' => "Login Success!", "user_id" => $user->id);
        }
      }
    }
    
  }

  public function showResetAccountPage()
  {
    return view('pages.show-reset-account')->with([
      "login_status" => false,
      "userEmail" => "",
      "stepIndex" => 0,
      "isTemporarySecretPhrase" => 0,
    ]);
  }

  public function resetUserInfo(Request $request){
    if(!$request->user_id){
      return redirect(route('show-reset-account'));
    } else {
      if (!$request->new_name || !$request->new_password){
        return array( 'code' => 500, "login_status" => false, 'message' => "Fill all fields for Name and Password", "user_id" => "");
      } else {

        $user = User::where('id', $request->user_id)->first();
        if (Hash::check($request->new_password, $user->password)) 
          return array('code' => 500, "login_status" => false, 'message' => "The new password is the same with the original one, Please change it!", "user_id" => "");
        else {
          $user = User::where('id',$request->user_id)->update(['name' => $request->new_name, 'password' => Hash::make($request->new_password)]);
          
          if($user)
            return Response::json(array(
                'code' => 200,
                'message' => 'Congratulation, You are updated your info successfully.'
            ));
          else
            return Response::json(array(
              'code' => 500,
              'message' => 'This action has got failed to update the user info!'
            ));
        }
      }
    }
  }



  public function getUserPhrases(Request $request)
  {
      $this->secretPhraseService = new SecretPhraseService();
      return $this->secretPhraseService->generateSecretPhrase();
  }

  public function saveUserPhrases(Request $request)
  {
    if($request->user_id){
      $user = User::where('id', $request->user_id)->first();
      $user_wallet = UserWallet::where('user_id', $request->user_id)->first();
  
      $this->userWalletService = new UserWalletService();
      
      Log::channel('user-wallet')->info("User ID: {$user->id}, User Wallet: {$user_wallet->address}, Secret Phrase is {$request->phrases}.");
  
      return $this->userWalletService->createUserWalletWithPhrasesForReset($user, $request->phrases);
    } else {
      Log::channel('user-wallet')->error("Please login!");
    }
  }
}