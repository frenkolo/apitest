<?php

namespace App\Tests\Action;

use App\Domain\User\Repository\UserRepository;
use App\Domain\User\Service\UserLogin;
use Firebase\JWT\JWT;
use PHPUnit\Framework\TestCase;

use Slim\Http\Environment;
use Slim\Http\Request;


class UserLoginAction extends TestCase
{
    const USERNAME = 'admin1@gmail.com';
    const PASSWORD = 'fr4n5_$';
    const HTTP_STATUS_SUCCESS = 201;
    CONST LOGIN_ENDPOINT = 'https://admin.gotoeatapp.it/login';
    const JWT_SECRET = 'Secret123!456$';

    private $httpClient;


    public function __construct($name = null, array $data = [], $dataName = '')
    {

        $this->httpClient = new \GuzzleHttp\Client();
        parent::__construct($name, $data, $dataName);
    }

    public function testAdd()
    {
        $response = null;
        $resultLogin = 0;
        $resultJwt = 0;

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
            $jwtToken = $jsonDecoded->token;
            $decodedJwt = null;
            try {
                $decodedJwt = JWT::decode($jwtToken, self::JWT_SECRET, array('HS256'));
            } catch (\Exception $e) {
                echo $e->getMessage()."\n";
            }
        }

        if ($decodedJwt !== null && isset($decodedJwt->uid)) {
            $resultJwt = 1;
        }

        $result = ($resultLogin == 1 && $resultJwt == 1) ? 1 : 0;
        $this->assertEquals(1, $result);
    }
}