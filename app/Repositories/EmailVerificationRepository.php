<?php
namespace App\Repositories;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\Crypt;

class EmailVerificationRepository extends BaseRepository{

	/**
	 * @param EmailVerification $emailVerification
	 */
	public function __construct(EmailVerification $emailVerification){
		$this->model = $emailVerification;
	}

	/**
	 * @param  Array $params
	 * @return EmailVerification
	 */
	public function findOrSave($params){
		$emailVerify = $this->findBy('email_id', $params['email_id']);
		if (empty($emailVerify)) {
	        $emailVerify = new EmailVerification;
	        $emailVerify->email_id = $params['email_id'];
	        $emailVerify->verification_code = $this->generateConfirmationCode();
	        $emailVerify->is_verified = false;
	        $emailVerify->save();
		}

        return $emailVerify;
	}

	/**
	 * Verify email against verification code
	 * @param  String $enc_email_id
	 * @param  String $code
	 * @return Boolean
	 */
	public function verifyEmail($enc_email_id, $code){
		try{
			$emailVerify = $this->model->where('email_id', Crypt::decryptString($enc_email_id))
        			->where('verification_code', $code)
        			->firstOrFail();
		}
		catch(\Exception $e){
			return false;
		}

		$emailVerify->markVerified();
		return true;
	}

	/**
	 * Generate confirmation code
	 * @return String
	 */
	public function generateConfirmationCode(){
		return str_random(30);
	}
}