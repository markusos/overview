<?php

use \League\OAuth2\Client\Provider\AbstractProvider;
use \League\OAuth2\Client\Token\AccessToken;

// Basecamp OAuth
class BasecampOAuth extends AbstractProvider {

	public $responseType = 'json';

	public function getAuthorizationUrl($options = []) {
        $this->state = isset($options['state']) ? $options['state'] : md5(uniqid(rand(), true));
        $params = [
            'type' => 'web_server',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'state' => $this->state,
            'scope' => is_array($this->scopes) ? implode($this->scopeSeparator, $this->scopes) : $this->scopes,
            'response_type' => isset($options['response_type']) ? $options['response_type'] : 'code',
            'approval_prompt' => isset($options['approval_prompt']) ? $options['approval_prompt'] : 'auto',
        ];
        return $this->urlAuthorize() . '?' . $this->httpBuildQuery($params, '', '&');
    }

	public function urlAuthorize() {
		return 'https://launchpad.37signals.com/authorization/new';
	}

	public function urlAccessToken() {
		return 'https://launchpad.37signals.com/authorization/token?type=web_server';
	}

	public function urlUserDetails(AccessToken $token) {
		return 'https://launchpad.37signals.com/authorization.json?access_token=' . $token;
	}

	public function userDetails($response, AccessToken $token) {
		$user = [
			'uid' => $response->identity->id,
			'firstName' => $response->identity->first_name,
			'lastName' => $response->identity->last_name,
			'email' => $response->identity->email_address
		];

		return $user;
	}

	public function userUid($response, AccessToken $token) {
		return $response->identity->id;
	}

	public function userEmail($response, AccessToken $token) {
		return $response->identity->email_address;
	}

	public function userScreenName($response, AccessToken $token) {
		return $response->identity->email_address;
	}
}
