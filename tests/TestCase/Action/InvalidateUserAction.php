<?php

namespace App\Tests\Action;

use Firebase\JWT\JWT;
use PHPUnit\Framework\TestCase;


use Slim\Http\Environment;
use Slim\Http\Request;


class InvalidateUserAction extends TestCase
{
    const USERNAME = 'admin1@gmail.com';
    const PASSWORD = 'fr4n5_$';
    const USERNAME_TO_INVALIDATE = 'operator1@gmail.com';
    const HTTP_STATUS_SUCCESS = 201;
    CONST LOGIN_ENDPOINT = 'https://admin.gotoeatapp.it/login';
    CONST INVALIDATE_ENDPOINT = 'https://admin.gotoeatapp.it/invalidate';
    const JWT_SECRET = 'Secret123!456$';

    private $httpClient;
    private $jwtToken;
    private $decodedJwt;


    public function __construct($name = null, array $data = [], $dataName = '')
    {

        $this->httpClient = new \GuzzleHttp\Client();
        parent::__construct($name, $data, $dataName);
    }

    private function login()
    {
        $response = null;
        $resultLogin = 0;

        try {
            $response = $this->httpClient->post(self::LOGIN_ENDPOINT,
                array(
                    'form_params' => array(
                        'username' => self::USERNAME,
                        'password' => self::PASSWORD
                    )
                )
            );
        } catch (\Exception $e) {
            echo $e->getMessage()."\n";
        }
        if ($response !== null && $response->getStatusCode() == self::HTTP_STATUS_SUCCESS) {
            $resultLogin = 1;
        }

        if ($resultLogin == 1) {
            $body =$response->getBody();
            $jsonDecoded = json_decode($body);
            $this->jwtToken = $jsonDecoded->token;
            $this->decodedJwt = null;
            try {
                $this->decodedJwt = JWT::decode($this->jwtToken, self::JWT_SECRET, array('HS256'));
            } catch (\Exception $e) {
                echo $e->getMessage()."\n";
            }
        }
        if ($this->decodedJwt == null || !isset($this->decodedJwt->uid)) {
            return false;
        }
        return true;
    }

    private function invalidate()
    {
        $decodedResponse = null;
        try {
            $response = $this->httpClient->post(self::INVALIDATE_ENDPOINT,
                array(
                    'form_params' => array(
                        'username' => self::USERNAME_TO_INVALIDATE
                    ),
                    'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $this->jwtToken]
                )
            );
           $decodedResponse = json_decode($response->getBody());
        } catch (\Exception $e) {
            echo $e->getMessage()."\n";
        }

        if ($decodedResponse !== null && isset($decodedResponse->result) && $decodedResponse->result == "OK") {
            return true;
        }
        return false;

    }

    public function testAdd()
    {

        if ($this->login() !== true) {
            $this->assertEquals(1, 0);
            return;
        }

        if ($this->invalidate() !== true) {
            $this->assertEquals(1, 0);
            return;
        }
        $this->assertEquals(1, 1);
    }
}