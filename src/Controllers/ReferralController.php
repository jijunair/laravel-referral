<?php

namespace Jijunair\LaravelReferral\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;

class ReferralController extends Controller
{
    /**
     * Assign a referral code to the user.
     *
     * @param  string  $referralCode
     * @return RedirectResponse
     */
    public function assignReferrer($referralCode)
    {
        $refCookieName = config('referral.cookie_name');
        $refCookieExpiry = config('referral.cookie_expiry');
        if (Cookie::has($refCookieName)) {
            // Referral code cookie already exists, redirect to configured route
            return redirect()->route(config('referral.redirect_route'));
        } else {
            // Create a referral code cookie and redirect to configured route
            $ck = Cookie::make($refCookieName, $referralCode, $refCookieExpiry);
            return redirect()->route(config('referral.redirect_route'))->withCookie($ck);
        }
    }

    /**
     * Generate referral codes for existing users.
     *
     * @return JsonResponse
     */
    public function createReferralCodeForExistingUsers()
    {
        $userModel = resolve(config('auth.providers.users.model'));
        $users = $userModel::cursor();

        foreach ($users as $user) {
            if (!$user->hasReferralAccount()) {
                $user->createReferralAccount();
            }
        }

        return response()->json(['message' => 'Referral codes generated for existing users.']);
    }
}
