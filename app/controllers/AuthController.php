<?php

class AuthController extends BaseController {

	private $basecamp;

	public function __construct(BasecampOAuth $basecamp) {
		$this->basecamp = $basecamp;
	}

	protected function oauth_url()
	{
		$authUrl = $this->basecamp->getAuthorizationUrl();
		Session::put('oauth2state', $this->basecamp->state);

		return Redirect::to($authUrl);
	}

	protected function oauth_callback()
	{
		$state = Input::get('state');

		if (empty($state) || $state !== Session::get('oauth2state')) {
			Session::forget('oauth2state');
			exit('Invalid state');
		}
		else {
			// Try to get an access token (using the authorization code grant)
            try {
                $token = $this->basecamp->getAccessToken('authorization_code', [
                    'code' => Input::get('code')
                ]);
            }
            catch (League\OAuth2\Client\Exception\IDPException $e) {
                return Redirect::to('/');
            }

			Session::put('accessToken', $token->accessToken);
			Session::put('user', $this->basecamp->getUserDetails($token));

            return Redirect::to('/authorization');
		}
	}

    protected function getAuthorization() {
        $accessToken = Session::get('accessToken');

        $client = new GuzzleHttp\Client([
            'base_url' => 'https://launchpad.37signals.com/authorization.json',
            'defaults' => [
                'query'   => ['access_token' => $accessToken],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => Config::get('basecamp.userAgent'),
                ],
                'future' => true
            ]
        ]);

        $response = json_decode($client->get()->getBody());
        $accounts = [];
        foreach ($response->accounts as $account) {
            if($account->product === 'bcx') {
                $accounts[] = $account;
            }
        }

        Session::put('accounts',  $accounts);

        if (count($response->accounts) === 0) {
            return "You do not have any valid basecamp accounts!";
        }
        if (count($response->accounts) === 1) {
            Session::put('apiUrl', $accounts[0]->href);
            return Redirect::to('/');
        }
        else {
            return View::make('account')->with('accounts', $accounts);
        }
    }

    protected function setAuthorization() {
        Session::put('apiUrl', Session::get('accounts')[Input::get('api')]->href);
        return Redirect::to('/');
    }

    protected function logout()
    {
        Session::forget('oauth2state');
        Session::forget('accessToken');
        Session::forget('user');

        return Redirect::to('/');
    }
}
