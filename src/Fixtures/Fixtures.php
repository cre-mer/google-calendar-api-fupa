<?php

declare(strict_types=1);

namespace Fixtures;

class Fixtures
{
    private $name;

    public function __construct(array $ids, array $seasons)
    {
        $this->ids = $ids;
        $this->seasons = $seasons;

        $this->optParams = [
          'orderBy' => 'startTime',
          'singleEvents' => true,
        ];
    }

    public function getFixtures() {
        foreach ($this->ids as $key => $id) {
            $results = $this->getService()->events->listEvents("$id@import.calendar.google.com", $this->optParams);
            $fixtures = $results->getItems();

            $this->updateFixtures($fixtures);
        }
    }

    public function getService()
    {
        // Get the API client and construct the service object.
        $client = $this->getClient();
        $service = new \Google_Service_Calendar($client);
        return $service;
    }

    /**
	 * Returns an authorized API client.
	 * @return \Google_Client the authorized client object
	 */
	private function getClient()
	{
	    $client = new \Google_Client();
	    $client->setApplicationName('Google Calendar API PHP Quickstart');
	    $client->setScopes(\Google_Service_Calendar::CALENDAR_READONLY);
	    $client->setAuthConfig(storage_path('config/credentials.json'));
	    $client->setAccessType('offline');
	    $client->setPrompt('select_account consent');

	    // Load previously authorized token from a file, if it exists.
	    // The file token.json stores the user's access and refresh tokens, and is
	    // created automatically when the authorization flow completes for the first
	    // time.
	    $tokenPath = storage_path('config/token.json');
	    if (file_exists($tokenPath)) {
	        $accessToken = json_decode(file_get_contents($tokenPath), true);
	        $client->setAccessToken($accessToken);
	    }

	    // If there is no previous token or it's expired.
	    if ($client->isAccessTokenExpired()) {
	        // Refresh the token if possible, else fetch a new one.
	        if ($client->getRefreshToken()) {
	            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
	        } else {
	            // Request authorization from the user.
	            $authUrl = $client->createAuthUrl();
	            // Exchange authorization code for an access token.
	            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
	            $client->setAccessToken($accessToken);

	            // Check to see if there was an error.
	            if (array_key_exists('error', $accessToken)) {
	                throw new Exception(join(', ', $accessToken));
	            }
	        }
	        // Save the token to a file.
	        if (!file_exists(dirname($tokenPath))) {
	            mkdir(dirname($tokenPath), 0700, true);
	        }
	        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
	    }
	    return $client;
	}
}
