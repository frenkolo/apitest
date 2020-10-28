<?php

namespace App\Action;

use App\Domain\User\Service\ChangePassword;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


final class ChangePasswordAction
{
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

        // Invoke the Domain with inputs and retain the result
        $res = $this->changePassword->changePassword($data["user"], $data["password"]);
        if (count($res) == 0) {
            $result = [ 'result' => 'KO', 'error' => 'login failed'];
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