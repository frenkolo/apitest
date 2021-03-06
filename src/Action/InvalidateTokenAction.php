<?php

namespace App\Action;

use App\Domain\User\Service\InvalidateToken;
use App\Domain\User\Service\RefreshJwt;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Firebase\JWT\JWT;



final class InvalidateTokenAction
{
    const JWT_SECRET = 'Secret123!456$';
    const ROLE_ADMIN = 1;
    const ROLE_OPERATOR = 2;
    const ROLE_USER = 3;


    private $invalidateToken;

    public function __construct(InvalidateToken $invalidateToken,  RefreshJwt $refreshJwt)
    {
        $this->invalidateToken = $invalidateToken;
        $this->refreshJwt = $refreshJwt;
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
        $userData = $this->refreshJwt->refreshJwt($decodedJwt->username);
        if ($userData[0]["banned"] == 1) {
            $result = [ 'result' => 'KO', 'error' => 'invalid session'];
            $response->getBody()->write((string)json_encode($result));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        if ((int)$userData[0]["role"] !== self::ROLE_ADMIN) {
            $result = [ 'result' => 'KO', 'error' => 'operation failed (not sufficient privileges)'];
            $response->getBody()->write((string)json_encode($result));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        $res = null;
        try {
            /* GLOBAL INVALIDATION IF NO USERNAME GIVEN */
            $username = isset($data["username"]) ? $data["username"] : null;
            $res = $this->invalidateToken->invalidateToken($username);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        if ($res === null) {
            $result = [ 'result' => 'KO', 'error' => 'operation failed'];
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