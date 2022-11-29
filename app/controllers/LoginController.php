<?php

use GuzzleHttp\Psr7\Response;

include_once "UsuarioController.php";
include_once "AutentificadorJWT.php";

class LoginController{

    public function cargarUsuario($request, $response, $args){

        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];

        $usuarioEncontrado = Usuario::obtenerUsuario($usuario);
      
        if($usuarioEncontrado != null){
            if(password_verify($clave, $usuarioEncontrado->clave) && $usuario == $usuarioEncontrado->usuario){ 

                $datos = array('usuario'=>$usuario,'tipo'=>$usuarioEncontrado->tipo, 'UsuarioId'=>$usuarioEncontrado->UsuarioId);
                $token = AutentificadorJWT::CrearToken($datos);
                $payload = json_encode(array('Respuesta' => 'OK','UsuarioId'=>$usuarioEncontrado->UsuarioId, 'usuario'=>$usuario, 'tipo' => $usuarioEncontrado->tipo,'token' => $token));
            }
            else{
                $payload = json_encode(array('mensaje' => "Usuario o clave incorrectos", 'status' => 401));
            }
        }
        else{
            $payload = json_encode(array('mensaje' => "Usuario o clave incorrectos", 'status' => 401));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
?>