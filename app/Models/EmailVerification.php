<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $fillable = ['email_id', 'verification_code'];

    public function markVerified(){
        $this->is_verified = true;
        $this->save();    	
    }
}
