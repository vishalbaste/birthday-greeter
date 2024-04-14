<?php
require_once __DIR__ . '/../vendor/autoload.php';

class Google
{
    const tokenAt = __DIR__.'/../token/credentials.json';
    private $errors = [];
    private $client;
    private $config;
    private $token;
    private $authCallback;
    private $PeopleService;

    public function __construct($authCallback = null)
    {
        if(file_exists(self::tokenAt) && is_readable(self::tokenAt))
        {
            $this->authCallback = $authCallback;
            date_default_timezone_set('Asia/Calcutta');
            $this->client = new Google_Client();
            $this->client->setAuthConfig(self::tokenAt);
            $this->client->addScope(Google_Service_PeopleService::CONTACTS_READONLY);
            $this->client->setAccessType('offline');
            $this->config = json_decode(file_get_contents(self::tokenAt));
            $this->token  = $this->getToken();
            $this->client->setAccessToken($this->token);
        }
        else
            array_push($this->errors,['name' => 'file','error' => 'token file not exists']);
    }

    private function auth()
    {
        try
        {
            if(is_callable($this->authCallback))
            {
                $this->auth_url = $this->client->createAuthUrl();
                call_user_func($this->authCallback,$this->auth_url,$this->config->web->baseUrl);
            }
            else
                throw new Exception('Google authentication is required.');                
        }
        catch(Exception | Error $e)
        {
            array_push($this->errors,['name' => 'auth','error' => $e->getMessage()]);
        }
    }

    public function getToken($code = null)
    {
        try
        {
            if(empty($this->config->refresh_token))
            {
                if(!empty($code) || !empty($_REQUEST['code']))
                {
                    $token = $this->client->fetchAccessTokenWithAuthCode($code ?? $_REQUEST['code']);
                    if(!empty($token['access_token']) && !empty($token['refresh_token']))
                    {
                        
                        $this->config->access_token  =  $token['access_token'];
                        $this->config->refresh_token =  $token['refresh_token'];
                        $this->config->expires_in    =  $token['expires_in'];
                        $this->config->created       =  $token['created'];
                        $this->config->scope         =  $token['scope'];

                        file_put_contents(self::tokenAt,json_encode($this->config));
                        return $token['access_token'];
                    }
                    else array_push($this->errors,['name' => 'tokenusingcode','error' => $token]);
                }
                else $this->auth();
            }
            else if(!empty($this->config->refresh_token) && !empty($this->config->expires_in) && is_int($this->config->created))
            {
                $createdMinutes = ((time() - $this->config->created) / 60);
                if(($createdMinutes <= $this->config->expires_in) && !empty($this->config->access_token))
                    return $this->config->access_token;
                else
                {
                    $responce = $this->client->fetchAccessTokenWithRefreshToken($this->config->refresh_token);

                    if(!empty($responce['access_token']) && !empty($responce['created']) && !empty($responce['expires_in']))
                    {
                        $this->config->access_token  =  $responce['access_token'];
                        $this->config->expires_in    =  $responce['expires_in'];
                        $this->config->created       =  $responce['created'];

                        file_put_contents(self::tokenAt,json_encode($this->config));
                        return $responce['access_token'];
                    }
                    else array_push($this->errors,['name' => 'refreshtoken','error' => $responce]);
                }
            }
            else
                array_push($this->errors,['name' => 'file','error' => 'Invalid credentials file.']);
        }
        catch(Exception | Error $e)
        {
            array_push($this->errors,['name' => 'token','error' => $e->getMessage()]);
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getContact()
    {
        $allConnections = [];
        $params = ['personFields' => 'names,emailAddresses,birthdays,genders,phoneNumbers', 'pageSize' => 200];
        $this->client->setAccessToken($this->getToken());
        $this->PeopleService = new Google_Service_PeopleService($this->client);
        $results = $this->PeopleService->people_connections->listPeopleConnections('people/me', $params);

        $allConnections = array_merge($allConnections, $results->getConnections());
        while ($results->getNextPageToken())
        {
            $params['pageToken'] = $results->getNextPageToken();
            $results = $this->PeopleService->people_connections->listPeopleConnections('people/me', $params);
            $allConnections = array_merge($allConnections, $results->getConnections());
        }
        echo '<pre>';
        print_r($allConnections);
    }
}