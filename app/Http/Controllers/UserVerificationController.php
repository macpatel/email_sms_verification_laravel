<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\EmailVerification;
use App\Models\MobileVerification;
use App\Mail\EmailVerificationLink;
use Illuminate\Support\Facades\Crypt;
use Mail;
use App\Library\T2Factors;
use App\Repositories\EmailVerificationRepository;
use App\Repositories\MobileVerificationRepository;

class UserVerificationController extends Controller
{
	/**
	 * @var EmailVerificationRepository
	 */
	protected $emailVerificationRepo;

	/**
	 * @var MobileVerificationRepository
	 */	
	protected $mobileVerificationRepo;

	/**
	 * @param EmailVerificationRepository  $emailVerificationRepo
	 * @param MobileVerificationRepository $mobileVerificationRepo
	 */
	public function __construct(EmailVerificationRepository $emailVerificationRepo,
								MobileVerificationRepository $mobileVerificationRepo){
		$this->emailVerificationRepo = $emailVerificationRepo;
		$this->mobileVerificationRepo = $mobileVerificationRepo;
	}

	/**
	 * Send Verification Email to email id in request
	 * @param  Request $request
	 * @return Json
	 */
    public function sendEmailVerification(Request $request){
        $rules = [
            'email_id' => 'required|email',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return $this->respondeWithError($validator->errors(), 400);
        }

        $emailVerify = $this->emailVerificationRepo->findOrSave($request->all());

        Mail::to($emailVerify->email_id)->send(new EmailVerificationLink($emailVerify));

        return $this->respondeWithSuccess('Verification Email sent to your Mail Id.');

    }

	/**
	 * Verify email in the request
	 * @param  Request $request
	 * @return Json
	 */
    public function verifyEmail(Request $request){
    	$emailVerify=null;
		$rules = [
		            'encrypted_email_id' => 'required',
		            'verification_code' => 'required',
		        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return $this->respondeWithError($validator->errors(), 400);
        }

        $enc_email_id = $request->input('encrypted_email_id');
        $code = $request->input('verification_code');

        if (!$this->emailVerificationRepo->verifyEmail($enc_email_id, $code)) {
        	return $this->respondeWithError('Email not Verified, Retry.');
        }

        return $this->respondeWithSuccess('Email Verified.');
    }

	/**
	 * Send OTP to mobile for
	 * @param  Request $request
	 * @return Json
	 */
    public function sendMobileVerification(Request $request){
        $rules = [
            'mobile_no' => 'required|digits:10',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return $this->respondeWithError($validator->errors(), 400);
        }

        $mobileVerify = $this->mobileVerificationRepo->findOrSave($request->all());

        $smsApi = new T2Factors();
        $smsResp = $smsApi->sendOTP($mobileVerify->mobile_no, $mobileVerify->otp);
        if ( strtolower($smsResp->Status) == 'success') {
        	return $this->respondeWithSuccess('OTP send to your Number.');
        }
        return $this->respondeWithError('Error in sending OTP.');

    }

	/**
	 * Verify mobile OTP
	 * @param  Request $request
	 * @return Json
	 */
    public function verifyMobile(Request $request){

		$rules = [
		            'mobile_no' => 'required|digits:10',
		            'OTP' => 'required',
		        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return $this->respondeWithError($validator->errors(), 400);
        }

        $mobile_no = $request->input('mobile_no');
        $otp = $request->input('OTP');

        if (!$this->mobileVerificationRepo->verifyOTP($mobile_no, $otp)) {
        	return $this->respondeWithError('Mobile number not Verified, Retry.');
        }
        return $this->respondeWithSuccess('Mobile number Verified.');

    }
}
