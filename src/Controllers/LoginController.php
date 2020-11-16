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
        $flagEmail = false;
        $body = $request->getParsedBody();
        if(!validatorPost(['clave'],$response)){
            return $response;
        }
        if(validatorPost(['email'],$response)){
            $flagEmail = true;
        }
        if(!$flagEmail ||  !validatorPost(['nombre'],$response) ){
            return $response;
        }

        $user = null;

        if($flagEmail){
            $user = User::where('email','=',$body['email'])
                    ->where('clave','=',$body['clave'])
                    ->get()->first();
        }else{
            $user = User::where('nombre','=',$body['nombre'])
                    ->where('clave','=',$body['clave'])
                    ->get()->first();
        }
        
        if( $user == null){
            $response->getBody()->write('No existe un usuario con ese email, Nombre o password');
            return $response->withStatus(404);
        }else{
            $resp = array('email' => $user['email'], 
                          'nombre' => $user['nombre'], 
                          'tipo' => $user['tipo'],
                          'id' => $user['id']
                    );
            $resp['token'] = $this->createJWT($resp);
            $response->getBody()->write(json_encode($resp));
            return $response->withStatus(200);
        }
    }

    public function create (Request $request, Response $response, $args) {
        $body = $request->getParsedBody();

        if(!validatorPost(['email','nombre','clave','tipo'],$response)){
            return $response;
        }
        if(User::where('email','=',$body['email'])->get()->first() != null){
            $response->getBody()->write('Ya existe un usuario con ese email');
            return $response->withStatus(404);
        }
        if(User::where('nombre','=',$body['nombre'])->get()->first() != null){
            $response->getBody()->write('Ya existe un usuario con ese nombre');
            return $response->withStatus(404);
        }

        $user = new User;
        $user->email = $body['email'];
        $user->clave = $body['clave'];
        $user->nombre = $body['nombre'];
        $user->tipo = $body['tipo'];

        if( $user->save() ){
            $response->getBody()->write('Usuario creado con exito');
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