<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        try {
            // Disable SSL verification for development (Windows WAMP fix)
            $guzzleClient = new \GuzzleHttp\Client([
                'verify' => false
            ]);
            
            $googleUser = Socialite::driver('google')
                ->setHttpClient($guzzleClient)
                ->stateless()
                ->user();
            
            // Get avatar URL and handle size parameter for smaller URLs
            $avatarUrl = $googleUser->getAvatar();
            
            if ($avatarUrl) {
                // For Google URLs, simplify to base URL with size parameter
                if (strpos($avatarUrl, 'googleusercontent.com') !== false) {
                    // Remove any existing size parameters and add s96-c
                    $avatarUrl = preg_replace('/=s\d+-c$/', '', $avatarUrl);
                    $avatarUrl = preg_replace('/\?.*$/', '', $avatarUrl);
                    $avatarUrl .= '=s96-c';
                }
                
                // Ensure URL is not too long (max 2000 chars for TEXT field)
                if (strlen($avatarUrl) > 2000) {
                    \Log::warning('Avatar URL too long, using null', ['url_length' => strlen($avatarUrl)]);
                    $avatarUrl = null;
                }
            }
            
            $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'name' => strip_tags($googleUser->getName()),
                    'email' => strip_tags($googleUser->getEmail()),
                    'avatar' => $avatarUrl,
                ]
            );

            Auth::login($user);

            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            
            return redirect()->to($frontendUrl . '?auth=success&token=' . $user->createToken('auth_token')->plainTextToken);
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            return redirect()->to($frontendUrl . '?auth=error&message=' . urlencode('Authentication failed. Please try again.'));
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
