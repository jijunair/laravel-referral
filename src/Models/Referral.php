<?php

namespace Jijunair\LaravelReferral\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'referral_code', 'referrer_id'
    ];

    /**
     * Get the user associated with the referral.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('referral.user_model'), 'user_id');
    }

    /**
     * Get the referrer associated with the referral.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referrer()
    {
        return $this->belongsTo(config('referral.user_model'), 'referrer_id');
    }

    /**
     * Retrieve the user by referral code.
     *
     * @param  string  $code
     * @return mixed|null
     */
    public static function userByReferralCode($code)
    {
        $referrer = self::where('referral_code',$code)->first();
        if ($referrer) {
            return App::make(config('referral.user_model'))->find($referrer->user_id);
        }
        return null;
        
    }
}
