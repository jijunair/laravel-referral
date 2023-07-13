<?php

namespace Jijunair\LaravelReferral\Traits;

use Illuminate\Support\Str;
use Jijunair\LaravelReferral\Models\Referral;

trait Referrable
{
    /**
     * Get the referrals associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Get the referral account of the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referralAccount()
    {
        return $this->belongsTo(Referral::class, 'id', 'user_id');
    }

    /**
     * Check if the user has a referral account.
     *
     * @return bool
     */
    public function hasReferralAccount()
    {
        return !is_null($this->referralAccount);
    }

    /**
     * Get the referral link for the user.
     *
     * @return string
     */
    public function getReferralLink()
    {
        if ($this->hasReferralAccount()) {
            return url('/') . "/" . config('referral.route_prefix') . "/" . $this->getReferralCode();
        }
        return "";
    }

    /**
     * Get the referral code of the user's referral account.
     *
     * @return string|null
     */
    public function getReferralCode()
    {
        if ($this->hasReferralAccount()) {
            return $this->referralAccount->referral_code;
        }
        
        return null;
    }

    /**
     * Create a referral account for the user.
     *
     * @param  int|null  $referrerID
     * @return void
     */
    public function createReferralAccount(int $referrerID = NULL)
    {

        $prefix = config('referral.ref_code_prefix');
        $length = config('referral.referral_length');
        $referralCode = $this->generateUniqueReferralCode($prefix, $length);

        $ref = new Referral;
        $ref->user_id = $this->getKey();
        $ref->referrer_id = $referrerID;
        $ref->referral_code = $referralCode;
        $ref->save();
    }

    /**
     * Generate a unique referral code.
     *
     * @param  string  $prefix
     * @param  int  $length
     * @return string
     */
    private function generateUniqueReferralCode($prefix, $length)
    {
        $prefix = strtolower($prefix);
        // Generate an initial referral code
        $code = $prefix . strtolower(Str::random($length));

        // Check if the generated code already exists in the database
        while (Referral::where('referral_code', $code)->exists()) {
            // If code already exists, generate a new one until a unique code is found
            $code = $prefix . strtolower(Str::random($length));
        }
        
        return $code;
    }
}
