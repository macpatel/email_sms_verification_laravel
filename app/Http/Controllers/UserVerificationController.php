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

class UserVerificationController extends Controller
{
    public function sendEmailVerification(Request $request){
        $rules = [
            'email_id' => 'required|email',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return $this->respondeWithError($validator->errors(), 400);
        }

        $confirmation_code = str_random(30);

        $emailVerify = EmailVerification::firstOrNew(["email_id" => $request->input('email_id')]);
        $emailVerify->email_id = $request->input('email_id');
        $emailVerify->verification_code = $confirmation_code;
        $emailVerify->is_verified = false;
        $emailVerify->save();

        Mail::to($emailVerify->email_id)->send(new EmailVerificationLink($emailVerify));

        return $this->respondeWithSuccess('Verification Email sent to your Mail Id.');

    }

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

        try{
        	$emailVerify = EmailVerification::where('email_id', Crypt::decryptString($request->input('encrypted_email_id')))
        								->where('verification_code', $request->input('verification_code'))
        								->firstOrFail();
        }
        catch(\Exception $e){
        	return $this->respondeWithError('Email not Verified, Retry.');
        }

        $emailVerify->markVerified();

        return $this->respondeWithSuccess('Email Verified.');
    }

    public function sendMobileVerification(Request $request){
        $rules = [
            'mobile_no' => 'required|digits:10',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return $this->respondeWithError($validator->errors(), 400);
        }

        $otp = mt_rand(1000,9000);

        $mobileVerify = MobileVerification::firstOrNew(["mobile_no" => $request->input('mobile_no')]);
        $mobileVerify->mobile_no = $request->input('mobile_no');
        $mobileVerify->otp = $otp;
        $mobileVerify->is_verified = false;
        $mobileVerify->save();

        $smsApi = new T2Factors();
        $smsResp = $smsApi->sendOTP($mobileVerify->mobile_no, $mobileVerify->otp);
        if ( strtolower($smsResp->Status) == 'success') {
        	return $this->respondeWithSuccess('OTP send to your Number.');
        }
        return $this->respondeWithError('Error in sending OTP.');

    }

    public function verifyMobile(Request $request){

    	$mobileVerify=null;
		$rules = [
		            'mobile_no' => 'required|digits:10',
		            'otp' => 'required',
		        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return $this->respondeWithError($validator->errors(), 400);
        }

        try{
        	$mobileVerify = MobileVerification::where('mobile_no', $request->input('mobile_no'))
        								->where('otp', $request->input('otp'))
        								->firstOrFail();
        }
        catch(\Exception $e){
        	return $this->respondeWithError('Mobile number not Verified, Retry.');
        }

        $mobileVerify->markVerified();

        return $this->respondeWithSuccess('Mobile number Verified.');

    }
}
