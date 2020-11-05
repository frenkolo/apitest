<?php


use Slim\App;

return function (App $app) {

    $app->post('/login', \App\Action\UserLoginAction::class);
    $app->post('/chgpassword', \App\Action\ChangePasswordAction::class)
            ->add(\PsrJwt\Factory\JwtMiddleware::html('Secret123!456$', 'jwt', 'Authorization Failed'));

};

