<?php

function validatorPost($params,$response){
    $retorno = true;
    for ($i=0; $i < count($params); $i++) { 
        if( !isset($_POST[$params[$i]]) ){
            $response->getBody()->write("El $params[$i] es obligatorio");
            $retorno = false;
            break;
        }
    }
    return $retorno;
}