<?php

namespace App\Services;

use App\Repository\UserRepository;
use Storage;
use File;
use Auth;
use JWTAuth;
use JWTAuthException;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserService {
	
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo     = $userRepo;
    }


    /**
     *  create a new user  service function
     * 
     * @param array $request
     * @return  array
     */
    public function register($request)
    {
        
        return  $this->userRepo->register($request);
    }

    /**
     *  create a user address service function
     * 
     * @param array $request
     * @return  array
     */
    
    public function apiLogin($request)
    {        
        return $this->userRepo->apiLogin($request);
        
    } 



    public function verifyOtp($otp,$mobile)
    {
        return $this->userRepo->verifyOtp($otp,$mobile);
    }
    /**
     *  To get a user address service function
     * 
     * @param 
     * @return  
     */

    /**
     *  create a user update service function
     * 
     * @param array $request
     * @return  array
     */

    public function updateProfile($request)
    {        
        $hasUpdate = $this->userRepo->updateProfile($request->all());
        if($hasUpdate != false)
        {
            return ['status' =>true,'message'=> 'Profile updated successfully'];
        }
        else{
             return ['status'=>false,'message' => 'User Not found','type'=>'404'];
        }
    } 


     public static function getAuthenticatedUser(){
       
        try {
        
            if (! $userDetail = UserRepository::isValidUser()) {
                    return ['status'=>false,'message' => 'user_not_found','type'=>'404'];
            }

        }catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return ['status'=>false,'message' => 'token_expired','type' => $e->getStatusCode()];

        }catch(Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return ['status'=>false,'message' => 'token_invalid' ,'type' =>$e->getStatusCode()];

        }catch(Tymon\JWTAuth\Exceptions\JWTException $e) {

            return ['status'=>false,'message' => 'token_absent','type' =>$e->getStatusCode()];
        }

        if($userDetail != null)
        {   
                return ['status'=>true,'type'=>'200','data'=>$userDetail];
        }
    }


    /**
     *  To get a user image in service function
     * 
     * @param array $request
     * @return  array
     */
    public function getUserImage($token)
    {        
       return  $this->userRepo->getUserImage($token);
       
    } 

    /**
     *  to save the user image in service function
     * 
     * @param array $request
     * @return  array
     */
    public function saveUserImage($request)
    {
         return  $this->userRepo->saveUserImage($request);
    }

    public function changePassword($request){
         return $this->userRepo->changePassword($request->all());
    }

    public function getAddress()
    {        

        $getAddress = $this->userRepo->getAddress();

        if($getAddress != false)
        {
            return ['status' =>true,'message'=> 'Address get successfully','userAddress' => $getAddress];
        }
        elseif($getAddress == null){
              return ['status'=>true,'message' => 'Result Not found','userAddress' => $getAddress,'type'=>'200'];
        }else{
             return ['status'=>false,'message' => 'User Not found','type'=>'404'];
        }
    } 

    public function deleteAddress($address_id)
    {
        $id = base64_decode($address_id);

        $user = Auth::user();
        if($user != null && $user->isconfirmed == 1){  
            $deleteAddress = $this->userRepo->deleteAddress($id,$user->id);
            if($deleteAddress != false)
            {
                 return ['status' =>true,'message'=> 'Address delete successfully'];
            }else{
                return ['status'=>false,'message' => 'Address Not found','type'=>'404'];
            }
        }else{
            return ['status'=>false,'message' => 'User Not found','type'=>'404'];
        }

    }

    public static  function isValidUser()
    {
        return UserRepository::isValidUser();
    }
}