<?php

class TODOController extends BaseController {

	// App route function
	public function toDoList() {
		return View::make('todo');
	}

	// API route function
	public function api() {
		if(Session::has('accessToken')) {
            $accessToken = Session::get('accessToken');
            $apiUrl = Session::get('apiUrl');
            $userAgent =  Config::get('basecamp.userAgent');

            $basecampApi = new BasecampAPI($apiUrl, $accessToken, $userAgent);
			$people = $basecampApi->getAllTODOs();

			return Response::json($people);
		}
		else {
			return Response::json(['status' => 'Not authenticated']);
		}
	}
}
