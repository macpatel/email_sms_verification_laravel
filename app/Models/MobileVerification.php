<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileVerification extends Model
{
    protected $fillable = ['mobile_no', 'otp'];

    public function markVerified(){
        $this->is_verified = true;
        $this->save();    	
    }
}
