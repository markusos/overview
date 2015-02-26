<?php

class AuthController extends BaseController {

	private $basecamp;

	public function __construct(BasecampOAuth $basecamp) {
		$this->basecamp = $basecamp;
	}

    // Get and redirect to Basecamps oauth URL
	protected function oauth_url()
	{
		$authUrl = $this->basecamp->getAuthorizationUrl();
		Session::put('oauthState', $this->basecamp->state);
		return Redirect::to($authUrl);
	}

    // Callback function for Oauth
	protected function oauth_callback()
	{
		$state = Input::get('state');

		if (empty($state) || $state !== Session::get('oauthState')) {
            $this->clearSession();
            return View::make('todo')->with('error', "Invalid state");
		}
		else {
			// Try to get an access token (using the authorization code grant)
            try {
                $token = $this->basecamp->getAccessToken('authorization_code', [
                    'code' => Input::get('code')
                ]);

                Session::put('accessToken', $token->accessToken);
                Session::put('user', $this->basecamp->getUserDetails($token));
                return Redirect::to('/authorization');
            }
            catch (Exception $e) {
                $this->clearSession();
                return View::make('todo')->with('error', "Failed to authenticate with Basecamp");
            }
		}
	}

    // Get user Authorization for their Basecamp account.
    protected function getAuthorization() {
        try {
            // Try to authorize the user with the API
            $accessToken = Session::get('accessToken');
            $userAgent = Config::get('basecamp.userAgent');
            $auth = BasecampAPI::authorize($accessToken, $userAgent);
        }
        catch(Exception $e) {
            $this->clearSession();
            return View::make('todo')->with('error', "Authorization failed");
        }

        // Filter out non "new" basecamp accounts
        $accounts = [];
        foreach ($auth->accounts as $account) {
            if($account->product === 'bcx') {
                $accounts[] = $account;
            }
        }
        Session::put('accounts',  $accounts);

        if (count($accounts) === 0) {
            // User has no basecamp account
            $this->clearSession();
            return View::make('todo')->with('error', "You do not have any valid Basecamp accounts!");
        }
        if (count($accounts) === 1) {
            // User has one basecamp account, use that.
            Session::put('apiUrl', $accounts[0]->href);
            return Redirect::to('/');
        }
        else {
            // User has multiple Basecamp accounts. Ask which one to use
            return View::make('account')->with('accounts', $accounts);
        }
    }

    // Set the Authorized Basecamps API URL for future requests
    protected function setAuthorization() {
        Session::put('apiUrl', Session::get('accounts')[Input::get('api')]->href);
        return Redirect::to('/');
    }

    // Handle user logout
    protected function logout()
    {
        $this->clearSession();
        return Redirect::to('/');
    }

    // Clear all stored session data
    private function clearSession() {
        Session::forget('oauthState');
        Session::forget('accessToken');
        Session::forget('apiUrl');
        Session::forget('user');
    }
}
