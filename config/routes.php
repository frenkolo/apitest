<?php

define("SECRET_JWT", 'Secret123!456$');

use Slim\App;

return function (App $app) {

    $app->post('/login', \App\Action\UserLoginAction::class);
    $app->post('/chgpassword', \App\Action\ChangePasswordAction::class)
            ->add(\PsrJwt\Factory\JwtMiddleware::html(SECRET_JWT, 'jwt', 'Authorization Failed'));

};

