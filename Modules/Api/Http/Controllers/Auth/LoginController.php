<?php

namespace Modules\Api\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Api\Http\Controllers\Controller;
use Modules\Api\Http\Requests\LoginRequest;
use App\Services\UserService;


class LoginController extends Controller
{
    /**
        * Create a new controller instance.
        *
        * @return void
    */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

     /**
     * To login the  user .
     * @param  Request $request array
     * @return Response
     */
    public function apiLogin(Request $request)
    {   
       
        $data = $request->all();

            $validatedData = $this->validate($request,[
                'mobile' => 'required|regex:/^[0-9]{10}$/',
            ]);
            $login = $this->userService->apiLogin($request->all());
          
            $this->setMessage($login['message']);
            if($login['status'] == true)
            {
                $userid = base64_encode($login['user']['id']);
                $this->setResponseData(['userid' => $userid,'otp' => $login['user']['otp']]);
                $this->setstatus(200);
                return $this->toResponse();
            }else{
                $this->setErrors(['error'=>$login['message']]);
                $this->setStatus($login['type']);
                return $this->toResponse();
            }
    }

    /**
        * verify the otp .
        *  @param $request mobile_no,otp
        * @return 
    */
    public function verifyOtp($otp,$mobile){

        $verify = $this->userService->verifyOtp($otp,$mobile);
        if($verify['status'] == true)
        {
            $this->setMessage($verify['message']);
            $this->setResponseData([ 'apitoken' => $verify['apiToken'] ]);
            $this->setstatus(200);
            return $this->toResponse(); 
        }else{
            $this->setMessage($verify['message']);
            $this->setErrors(['error'=>[$verify['message']]]);
            return $this->toResponse();
        }
    }


    public function logout()
    {
        auth()->logout();
        $this->setMessage('user logout successfully');
        $this->setResponseData(['user logout successfully']);
        $this->setstatus(200);
        return $this->toResponse();
    }
}
