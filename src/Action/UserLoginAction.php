<?php

namespace App\Action;

use App\Domain\User\Service\UserLogin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


final class UserLoginAction
{
    private $userLogin;

    public function __construct(UserLogin $userLogin)
    {
        $this->userLogin = $userLogin;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        // Collect input from the HTTP request
        $data = (array)$request->getParsedBody();

        // Invoke the Domain with inputs and retain the result
        $res = $this->userLogin->loginUser($data["user"], $data["password"]);
        if (count($res) == 0) {
            $result = [ 'result' => 'KO', 'error' => 'login failed'];
            $response->getBody()->write((string)json_encode($result));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
        $factory = new \PsrJwt\Factory\Jwt();
        $builder = $factory->builder();
        $token = $builder->setSecret('Secret123!456$')
            ->setPayloadClaim('uid', $res[0]["id"])
            ->setPayloadClaim('email', $res[0]["email"])
            ->setPayloadClaim('username', $res[0]["username"])
            ->setPayloadClaim('first_name', $res[0]["first_name"])
            ->setPayloadClaim('last_name', $res[0]["last_name"])
            ->build();

        $result = [
            'result' => 'OK', 'token' => $token->getToken()
        ];

        $response->getBody()->write((string)json_encode($result));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }
}