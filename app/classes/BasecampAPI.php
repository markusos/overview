<?php

class BasecampAPI {

    private $client;

    public function __construct($apiUrl, $accessToken, $userAgent) {
        $this->client = $this->createBasecampClient($apiUrl, $accessToken, $userAgent);
    }

    public static function authorize($accessToken, $userAgent) {
        $client = new GuzzleHttp\Client([
            'base_url' => 'https://launchpad.37signals.com/authorization.json',
            'defaults' => [
                'query'   => ['access_token' => $accessToken],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => $userAgent,
                ],
                'future' => true
            ]
        ]);

        try {
            return json_decode($client->get()->getBody());
        }
        catch(Exception $e) {
            throw new Exception("Authorization failed");
        }
    }

    // Create a basecamp HTTPS client
    private function createBasecampClient($apiUrl, $accessToken, $userAgent) {
        return new GuzzleHttp\Client([
            'base_url' => $apiUrl . '/',
            'defaults' => [
                'query'   => ['access_token' => $accessToken],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => $userAgent
                ],
                'future' => true
            ]
        ]);
    }

    // Get all TODOs
    public function getAllTODOs() {
        $people = $this->getPeople();
        $projects = $this->getProjects();

        $page = 1;
        $requests = $this->createProjectTODORequests($projects, $page);

        while (count($requests) > 0) {
            $nextPageProjects = [];
            $response = GuzzleHttp\Pool::batch($this->client, $requests);
            $results = $this->decodeAPIResponse($response);

            // Process responses
            foreach ($results as $key => $result) {
                if (count($result) === 50) {
                    $nextPageProjects[] = $projects[$key];
                }

                $this->processProjectTODOs($result, $projects[$key]->name, $people);
            }

            $page++;
            $projects = $nextPageProjects;
            $requests = $this->createProjectTODORequests($projects, $page);
        }

        $this->removePeopleWithoutTODOs($people);
        $this->sortPeople($people);

        return $people;
    }

    // Get all active projects from the basecamp API
    public function getProjects() {
        $projects = [];
        $response = $this->client->get('projects.json');
        $data = json_decode($response->getBody());

        // Add all persons from basecamp
        foreach ($data as $project) {
            $p = new Project();
            $p->id = $project->id;
            $p->name = $project->name;
            $projects[] = $p;
        }

        return $projects;
    }

    // Get all People from the basecamp API
    public function getPeople() {
        $people = [];
        $response = $this->client->get('people.json');
        $data = json_decode($response->getBody());

        // Add all persons from basecamp
        foreach ($data as $person) {
            $p = new Person();
            $p->id = (string) $person->id;
            $p->name = $person->name;
            $p->avatar = $person->avatar_url;
            $p->todos = [];
            $people[$p->id] = $p;
        }

        // Create person for unassigned projects
        $p = new Person();
        $p->id = '0';
        $p->name = "Unassigned";
        $p->avatar = "/img/avatar.gif";
        $p->todos = [];
        $people[$p->id] = $p;

        return $people;
    }

    // Filter out people without any assigned TODOs
    private function removePeopleWithoutTODOs(&$people) {
        // Remove people with zero TODOs
        foreach ($people as $key=>$person) {
            if (count($person->todos) === 0) {
                unset($people[$key]);
            }
        }
    }

    // Sort People by their name
    private function sortPeople(&$people) {
        // Sort by name
        usort($people, function($a, $b) {
            return strcmp($a->name, $b->name);
        });
    }

    // Create Basecamp Api Project TODOs requests
    private function createProjectTODORequests($projects, $page) {
        $requests = [];

        foreach ($projects as $project) {
            $requests[] = $this->client->createRequest('GET', 'projects/'. $project->id .'/todos.json?page=' . $page);
        }
        return $requests;
    }

    // Decode Basecamp API json response
    private function decodeAPIResponse($response) {
        $results = [];

        foreach ($response as $key => $result) {
            if ($result->getStatusCode() === 200) {
                $results[] = json_decode($result->getBody());
            }
            else {
                die('Error: Invalid status code'); // TODO: Throw exception?
            }
        }
        return $results;
    }

    // Process all found TODOs for a project and sort them to the correct person array
    private function processProjectTODOs($projectTODOs, $projectName, &$people) {
        foreach ($projectTODOs as $todo) {
            if (isset($todo->completer)) {
                continue;
            }

            $t = new Todo();
            $t->project = $projectName;
            $t->content = $todo->content;
            $t->link = $todo->app_url;
            $t->due_on = $todo->due_on;

            $personId = isset($todo->assignee) ? $todo->assignee->id : 0;
            $people[$personId]->todos[] = $t;
        }
    }
}
