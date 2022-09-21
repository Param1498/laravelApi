<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ResponseTrait;

class RegisterRequest extends FormRequest
{
    use ResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'      => 'required|string|max:100',
            'mobile_no' => 'required|regex:/^[0-9]{10}$/|unique:users',
            'password'  => 'required|string|min:6',
        ];
    }

    /*
    * To show validation error that apply to the request.
    *
    **/
    public function failedValidation(Validator $validator){  
        $errors = (new ValidationException($validator))->errors();
        $this->setErrors($errors);
        $this->setMessage('Some error occur');
        throw new HttpResponseException($this->toResponse());
    }

}
