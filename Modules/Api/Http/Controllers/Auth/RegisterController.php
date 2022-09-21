<?php

namespace Modules\Api\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Api\Http\Controllers\Controller;
use Modules\Api\Http\Requests\RegisterRequest;
use App\Services\UserService;

class RegisterController extends Controller
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

    public function register(RegisterRequest $request)
    {
        $user = $this->userService->register($request->all());
        if($user['status'] == true)
        {
            $this->setMessage($user['message']);
            $this->setResponseData(['message' => $user['message']]);
            // $this->setResponseData(['otpstatus' => $user['otpstatus'],'message' => $user['message']]);
            return $this->toResponse();
        }else{
            $this->setMessage('Some error occur');
            $this->setErrors(['error'=>'some error occur']);
            return $this->toResponse();
        }
    }

     

    
}
