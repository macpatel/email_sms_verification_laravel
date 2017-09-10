<?php

namespace App\Library;
/**
 * This is a SMS sending library for https://2factor.in
 * https://2fa.api-docs.io/v1/send-sms-otp
 */
use GuzzleHttp\Client;

class T2Factors {

	private $apiEndPoint = 'https://2factor.in/API/V1/';

	public function __construct(){
		$this->apiEndPoint .= env('SMS_API_KEY') . '/SMS';
	}

	public function sendOTP($mobile_no, $otp='')
	{
		$this->apiEndPoint .= '/' . $mobile_no;

		if ( !empty($otp) ) {
			//custom otp
			$this->apiEndPoint .= '/' . $otp;
		}
		else{
			//auto generate otp
			$this->apiEndPoint .= '/AUTOGEN';
		}

		return $this->executeAPIRequest();

	}

	protected function executeAPIRequest(){
		$client = new Client();
        $res = $client->request('POST', $this->apiEndPoint );
        return json_decode($res->getBody());
	}
}