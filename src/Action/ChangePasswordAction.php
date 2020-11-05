<?php

namespace App\Action;

use App\Domain\User\Service\ChangePassword;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Firebase\JWT\JWT;



final class ChangePasswordAction
{
    const JWT_SECRET = 'Secret123!456$';
    const ROLE_ADMIN = 1;
    const ROLE_OPERATOR = 2;
    const ROLE_USER = 3;


    private $changePassword;

    public function __construct(ChangePassword $changePassword)
    {
        $this->changePassword = $changePassword;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {

        // Collect input from the HTTP request
        $data = (array)$request->getParsedBody();
        $headers = $request->getHeaders();

        $jwt = str_replace('Bearer ', '', $headers["Authorization"][0]);
        $decodedJwt = JWT::decode($jwt, self::JWT_SECRET, array('HS256'));

        if ($decodedJwt->email !== $data["user"] && (int)$decodedJwt->role !== self::ROLE_ADMIN) {
            $result = [ 'result' => 'KO', 'error' => 'operation failed (not sufficient privileges)'];
            $response->getBody()->write((string)json_encode($result));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        $res = null;
        try {
            $res = $this->changePassword->changePassword($data["username"], $data["password"]);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        if ($res === null) {
            $result = [ 'result' => 'KO', 'error' => 'operation failed (no records changed)'];
            $response->getBody()->write((string)json_encode($result));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        $result = [
            'result' => 'OK'
        ];

        $response->getBody()->write((string)json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }
}