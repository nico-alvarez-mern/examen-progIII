<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \Firebase\JWT\JWT;
require('../src/helpers/helper.php');
require('../src/Models/User.php');
use App\Models\User;

class LoginController {

    public function login (Request $request, Response $response, $args) {
        $body = $request->getParsedBody();
        if(!validatorPost(['email','password'],$response)){
            return $response;
        }
        $user = User::where('email','=',$body['email'])
                    ->where('password','=',$body['password'])
                    ->get()->first();

        if( $user == null){
            $response->getBody()->write('No existe un usuario con ese email o password');
            return $response->withStatus(404);
        }else{
            $resp = array('email' => $user['email'] , 'name' => $user['name'] );
            $resp['token'] = $this->createJWT($resp);
            $response->getBody()->write(json_encode($resp));
            return $response->withStatus(200);
        }
    }

    public function create (Request $request, Response $response, $args) {
        $body = $request->getParsedBody();

        if(!validatorPost(['email','password','name'],$response)){
            return $response;
        }
        if(User::where('email','=',$body['email'])->get()->first() != null){
            $response->getBody()->write('Ya existe un usuario con ese email');
            return $response->withStatus(404);
        }

        $user = new User;
        $user->email = $body['email'];
        $user->password = $body['password'];
        $user->name = $body['name'];

        if( $user->save() ){
            $resp = array('email' => $body['email'] , 'name' => $body['name'] );
            $resp['token'] = $this->createJWT($resp);
            $response->getBody()->write(json_encode($resp));
            return $response->withStatus(201);
        }else{
            $response->getBody()->write('No se pudo crear el usuario');
            return $response->withStatus(500);
        }

    }

    private function createJWT($payload){
        $key = "example_key";
        $jwt = JWT::encode($payload,$key);
        return $jwt;
    }

}