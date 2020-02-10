<?php

namespace App\Http\Controllers\Auth;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response as Psr7Response;
use League\OAuth2\Server\Exception\OAuthServerException;
use Laravel\Passport\Http\Controllers\AccessTokenController as PassportAccessTokenController;

class AccessTokenController extends PassportAccessTokenController
{
    /**
     * Authorize a client to access the user's account.
     *
     * @param  ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function issueToken(ServerRequestInterface $request)
    {
        try {
            return $this->server->respondToAccessTokenRequest($request, new Psr7Response);
        } catch (ClientException $exception) {
            $error = json_decode($exception->getResponse()->getBody());

            throw OAuthServerException::invalidRequest(
                'access_token',
                object_get($error, 'error.message')
            );
        }
    }
}
