<?php
require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';

class EncuestaController extends Encuesta implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      
        $codigoDeMesa = $parametros['codigoDeMesa'];
        $puntuacionMesa = $parametros['puntuacionMesa'];
        $puntuacionRestaurant = $parametros['puntuacionRestaurant'];
        $puntuacionMozo = $parametros['puntuacionMozo'];
        $puntuacionCocinero = $parametros['puntuacionCocinero'];
        $encuesta = $parametros['encuesta'];


        // Creamos la encuesta
        $m = new Encuesta();
        $m->codigoDeMesa = $codigoDeMesa;
        $m->puntuacionMesa = $puntuacionMesa;
        $m->puntuacionRestaurant = $puntuacionRestaurant;
        $m->puntuacionMozo = $puntuacionMozo;
        $m->puntuacionCocinero = $puntuacionCocinero;
        $m->encuesta = $encuesta;
        $m->crearEncuesta();

        $payload = json_encode(array("mensaje" => "Encuesta creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos encuesta por codigoDeMesa
        $cod = $args['codigoDeMesa'];
        $encuesta = Encuesta::obtenerEncuesta($cod);
        $payload = json_encode($encuesta);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Encuesta::obtenerTodos();
        $payload = json_encode(array("listaEncuesta" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        Usuario::modificarUsuario($nombre);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
