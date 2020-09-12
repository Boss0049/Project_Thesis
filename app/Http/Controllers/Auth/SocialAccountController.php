<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Arr;
use Response;
use Illuminate\Support\Facades\File;

class SocialAccountController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->with(['hd' => 'mail.kmutt.ac.th'])->redirect();
        // return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information
     *
     * @return Response
     */
    public function handleProviderCallback(Request $request)
    {
        $googleuser = Socialite::driver('google')->stateless()->user();
        $params = [
            'grant_type' => 'social',
            'client_id' => '2',
            'client_secret' => '',
            'provider' => 'google',
            'scope' => '',
            'access_token' => $googleuser->token,
        ];
        $request->request->add($params);

        $requestToken = Request::create("oauth/token", "POST");
        $response = Route::dispatch($requestToken);

        $data = json_decode((string) $response->content(), true);
        $accessToken = Arr::get($data, 'access_token');
        $expiresIn = Arr::get($data, 'expires_in');

        // return response($accessToken)->action('Auth\SocialAccountController@loginnn');
        // return redirect('/login')->header('Content-Type', $accessToken);
        return view('oauth.callback', [
            'token' => $accessToken,
            'expires' => $expiresIn,
        ]);
    }
}
