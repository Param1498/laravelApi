<?php 
namespace App\Repository;

use Illuminate\Http\Request;
// use Illuminate\Http\Response;
use JWTAuth;
use App\Models\User;
use Hash;
use SendOtp;
use JWTAuthException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Image;
use Storage;
use File;
use Illuminate\Support\Facades\Response;
use Str;

class UserRepository 
{
    public $user;

    function __construct(User $user) {

      $this->user = $user;
    }

    /**
     * register the new user .
     * @param  Request $request array
     * @return Response
     */

    public function register($data)
    {
     
        $user = $this->user->create([
                'fName'       =>     $data['fName'],
                'lName'       =>     $data['lName'],
                'mobile'     =>  $data['mobile'],
                'email'      =>     $data['email'],
                'password'   =>    Hash::make($data['password']),
                ]);

         return ['status'=>true,'user'=>$user,'message' => 'user register successfully'];
    }


    /**
     * To login the  user .
     * @param  Request $request array
     * @return Response
     */
    public function apiLogin($request)
    {
        
        $user = User::where('mobile',$request['mobile'])->first();
        
        if($user != null)
        {
            // $otp = SendOtp::GetOTP($user);
            $otp = $this->generateOTP();
                $userexist = User::where('mobile', $request['mobile']);
                $userexist->update(['otp'=>$otp]);
                $newuser = $userexist->first();
        }else{
            $otp = $this->generateOTP();
            $data =[
                'mobile' => $request['mobile'],
                'api_token' => Str::random(60),
                'otp'   => $otp
            ];
            $newuser = $this->user->create($data);
        }
        try{
            if($newuser)
            {
             
                if( $newuser->status == 1) {
                // if($newuser->isconfirmed == 1 && $newuser->status == 1) {
                    if(!$token = JWTAuth::fromUser($newuser)) {
                        return ['status'=>false,'message'=> 'Invalid credential','type'=>400];
                    }else{
                       
                        return ['status' => true, 'message' => 'Login Successfully', 'user' => $newuser];
                        
                    }
                }else{
                    // if($newuser->status == 0){
                        return ['status'=> false,'message'=>'You are restricted by Super admin to login','type'=>400];
                    // }

                    // if($newuser->isconfirmed == 0){
                    //     return ['status'=> false,'message'=>'Please first verify your Account','type'=>400];
                    // }
                }
            }else{
                return ['status'=> false,'message'=>'User Not Found','type'=> 404];
            }
        }
        catch(JWTException $e){
            return ['status'=>false,'message'=>'could not create token','type'=>400];
        }
      
    }
    private function generateOTP(){
         $otp = mt_rand(1111,9999);
         return $otp;
    }

    public function verifyOtp($otp,$mobile)
    {

        // $mobile = $request->mobile_no;
        // $otp    = $request->otp;
         $user= User::where('mobile',$mobile)->first();
       
        if (!isset($user)) {
            return ['status'=>false ,'type'=>400,'message'=>'Invalid user'];
        }else{
            
            if($user->otp == trim($otp)){
                $token = JWTAuth::fromUser($user);
                $user->update(['api_token'=>$token,'isconfirmed'=>1]);
                return ['status'=>true,'message'=>'Account activated successfully','apiToken'=>$token];
            }else{
                return ['status'=>false,'message'=>'Invalid otp'];
            }

        }
    }
    

    /**
     * check if user exists and is confirmed
     * @return boolean user object if user is valid 
     */
    public static function isValidUser(){
          $userObject = JWTAuth::parseToken()->authenticate();
          if($userObject != null){     
              return $userObject;
          }
          return false;
    }

    /**
     * update record in the database
     * @param  array $data 
     * @return boolean       
     */
    public function updateProfile($data)
    {

        $user = $this->isValidUser();
        if($user)
        {
            $updateArray =[
                'name'      => $data['name'],
                'email'     => $data['email'],
                // 'password'  => Hash::make($data['password']),
            ];
            User::where('id',$user->id)->update($updateArray);
            return true;
        }else{
            return false;
        }
    }


    public function changePassword($data){
        $currentPassword = $data['currentpassword'];
        $newpassword     = $data['newpassword'];
        $confirmpassword = $data['confirmpassword'];
        $user = $this->isValidUser();
        if($user)
        {
            if (Hash::check($currentPassword, $user->password)) {
                if ($newpassword == $confirmpassword) {
                        $password = bcrypt($newpassword);
                        $user = User::where('id',$user->id)->update(['password' => $password]);

                    return ['status' => true,'message' => 'Password update successfully'];

                }else
                {
                    return ['status'=>false,'message'=>'newpassword not match with confirm password','type'=>400];
                }
            }else{
                    return ['status'=>false,'message'=>'currentpassword not match','type'=>400];
            }
        }else{
            return ['status'=>false,'message' => 'User Not found','type'=>'404'];
        }

    }

    public function getUserImage($token)
    {

        try{
            $user = JWTAuth::toUser($token);
          
             if($user != null)
            {
                $userid = $user->id;
                $getuser = User::with('attachments')->where('id',$userid)->first();

                if($getuser->attachments != null){

                    $attach = $getuser->attachments;
                    $path = str_replace('\\','/',Storage::disk('local')->getDriver()->getAdapter()->applyPathPrefix('profiles/image/'.$userid.'/'.$attach['image_name']));

                }else{
                    $path = str_replace('\\','/',Storage::disk('local')->getDriver()->getAdapter()->applyPathPrefix('default'.'/'.'no-image.jpg'));
                }
                    return ['status' => true,'data' => $path];
            }

        }catch (JWTException $e) {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {

                return ['status'=>false,'message'=>'token_expired','type' => $e->getStatusCode()];
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                  return ['status'=>false,'message'=>'token_invalid','type' => $e->getStatusCode()];

            }else{
                return ['status'=>false,'message'=>'Token is required','type' => $e->getStatusCode()];
            }
        }
    }

    /**
     *  to save the user image in  function
     * 
     * @param array $request
     * @return  array
     */
    public function saveUserImage($request)
    {

         $user = $this->isValidUser();
        if($user)
        {
            if($user->attachments != null)
            {
                $attach = $user->attachments;
               
                unlink(str_replace('\\','/',$attach->storage_path.'\\'.$attach->image_name));
                Attachment::where('id',$attach->id)->delete();
            }
            $getimagebase64 = $request->image;
           
            $image = str_replace('data:image/jpeg;base64,', '', $getimagebase64);
            $image = str_replace(' ', '+', $image);
            $imageName = str_random(10).'.'.'jpeg';
            $filePath = storage_path(). '/' .'app/profiles/image/'.$user->id .'/';
            if (!file_exists($filePath)) {
                mkdir($filePath, 0755, true);
            }

            \File::put(storage_path().'/'.'app/profiles/image/'.$user->id.'/'.$imageName, base64_decode($image),'public');
            $type = 'profile';
            $attachment = new Attachment;
            $attachment->image_name = $imageName;
            $attachment->storage_path = storage_path('app\\profiles\\image\\'.$user->id);
            $attachment->path = storage_path('app\\profiles\\image\\'.$user->id.'\\'.$imageName);
            $attachment->attachable_id = $user->id;
            $attachment->attachable_type = 'App\User';
            $attachment->type = $type;
            $attachment->save();

             return ['status'=>true,'message' => 'Attachment save successfully'];
        }else{
            return ['status'=>false,'message' => 'User Not found','type'=>'404'];
        }
    }

}