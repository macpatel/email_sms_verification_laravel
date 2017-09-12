<?php
namespace App\Repositories;
use App\Models\MobileVerification;
use Illuminate\Support\Facades\Crypt;

class MobileVerificationRepository extends BaseRepository{

	/**
	 * 
	 * @param MobileVerification $mobileVerification
	 */
	public function __construct(MobileVerification $mobileVerification){
		$this->model = $mobileVerification;
	}

	/**
	 * Find the mobile verification code, generate new OTP and save
	 * @param  Array $params
	 * @return MobileVerification
	 */
	public function findOrSave($params){
		$mobileVerify = $this->findBy('mobile_no', $params['mobile_no']);
		if (empty($mobileVerify)) {
	        $mobileVerify = new MobileVerification;
	        $mobileVerify->mobile_no = $params['mobile_no'];
	        $mobileVerify->otp = $this->generateOTP();
	        $mobileVerify->is_verified = false;
	        $mobileVerify->save();
		}else{
			//save new OTP
	    	$mobileVerify->otp = $this->generateOTP();
			$mobileVerify->save();
		}

        return $mobileVerify;
	}

	/**
	 * Verify OTP against mobile no
	 * @param  $mobile_no
	 * @param  $otp
	 * @return Boolean
	 */
	public function verifyOTP($mobile_no, $otp){
		try{
			$mobileVerify = $this->model->where('mobile_no', $mobile_no)
        			->where('otp', $otp)
        			->firstOrFail();
		}
		catch(\Exception $e){
			return false;
		}

		$mobileVerify->markVerified();
		return true;
	}

	/**
	 * Generate New OTP
	 * @return Integer
	 */
	public function generateOTP(){
		return mt_rand(1000,9000);
	}
}