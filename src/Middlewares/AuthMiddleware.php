<?php

namespace App\Middlewares;

use \Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
// use Psr\Http\Message\ResponseInterface as Response;

class AuthMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $valido = false; // valido token

        $payload = $this->validatorJWT($_SERVER['HTTP_TOKEN']);
        if($payload != null){
            $valido = true;
        }
        if (!$valido) {
            $response = new Response();
            $response->getBody()->write('prohibido pasar');
            return $response->withStatus(403);
        } else {
            $response = $handler->handle($request);
            $existingContent = (string) $response->getBody();
            $resp = new Response();
            $resp->getBody()->write($existingContent);
            return $resp;
        }
    }

    private function validatorJWT($token){
        $retorno = null;
        $key = "example_key";
        try {
            $data = JWT::decode($token, $key, array('HS256'));
            $retorno = $data;
        } catch (\Throwable $th) {
           $retorno = null;
        }
        return $retorno;
    }
}
