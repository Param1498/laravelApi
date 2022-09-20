<?php

namespace App\Traits;

trait ResponseTrait{

	/*
	* To set response status, default status is 200
	* 
	*/
	protected $DefaultResponseStatus = 200;

	/*
	To set response message, default message is OK 
	 */
	protected $DefaultMessage = 'Ok';

	/**
	 * To set errors list in ErrorBag array
	 * 
	 * */
	protected $ErrorBag = [];


	/**
	 * To set data for send in api
	 * 
	 * */
	protected $ResponseDataArray = [];


	/**
	 * To append default respose array
	 * 
	 * */
	protected $AppendResponseArray = [];

	/**
	 *  To change default status
	 * 
	 * */
	public function setStatus($statusCode){

		$this->DefaultResponseStatus = $statusCode;
	}


	/**
	 * To set response message
	 * 
	 * */
	public function setMessage($responseMessage){

		$this->DefaultMessage = $responseMessage;
	}


	/**
	 * 
	 * To set data for api response
	 * 
	 * */
	public function setResponseData(Array $dataArray){

		$this->ResponseDataArray = $dataArray;
	}

	/**
	 * To set list of errors in array
	 * 
	 * */
	public function setErrors(Array $errorsArray){

		$this->setStatus(400);		
		$this->ErrorBag = $errorsArray;
	}

	/**
	 * To apppend response array
	 * 
	 * */
	public function appendResponseArray(Array $appendArray){

		$this->AppendResponseArray = $appendArray;
	}

	/**
	 * Return back the json response
	 * 
	 * */
	public function toResponse(Array $responseData = [], $message = null, $status = null, Array $errors = []){
		if(!empty($errors)){
			$this->setErrors($errors);
		}
		if($status != null){
			$this->setStatus($status);
		}
		if($message != null){
			$this->setMessage($message);
		}
		if(!empty($responseData)){
			$this->setResponseData($responseData);
		}
		
		$responseArray = $this->generateResponse();


		return response()->json($responseArray,$this->getResponseCode());
	}


	/**
	 * To generate response array
	 * 
	 * */
	protected function generateResponse(){
		$response = [
						'message'	=>	$this->getMessage(),
						'data'		=>	$this->getResponseData(),
						'errors'	=>	$this->getErrors(),
						'code'		=>	$this->getResponseCode(),
					];
		if(!empty($this->AppendResponseArray)){
			$response = array_merge($response,$this->AppendResponseArray);
		}
		return $response;
	}

	/**
	 * To get response message
	 * 
	 * */
	public function getMessage(){

		return $this->DefaultMessage;
	}


	/**
	 * To get current response code
	 * 
	 * */
	public function getResponseCode(){

		return $this->DefaultResponseStatus;
	}

	/**
	 * To get errors bag
	 * 
	 * */
	public function getErrors(){

		return $this->ErrorBag;
	}


	/**
	 * To get api response data array
	 * 
	 * */
	public function getResponseData(){

		return $this->ResponseDataArray;
	}
}