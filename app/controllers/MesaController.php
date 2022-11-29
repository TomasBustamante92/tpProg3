<?php

use Illuminate\Support\Arr;

require_once './models/Mesa.php';
require_once './models/fpdf.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $codigoDeMesa = $parametros['codigoDeMesa'];

      // Creamos la mesa
      $m = new Mesa();
      $m->codigoDeMesa = $codigoDeMesa;
      $m->estadoDeMesa = "";
      $m->codigoPedido = "";
      $m->foto = "";
      $m->crearMesa();

      $payload = json_encode(array("mensaje" => "Mesa creada con exito"));
    
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function Cobrar($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $codigoDeMesa = $_GET['codigoDeMesa'];

      $payload = Mesa::CrearPdf($codigoDeMesa);
      $precioTotal = Mesa::CalcularPrecioTotal($codigoDeMesa);

      $pedido = Pedido::obtenerIdPorCodigo($codigoDeMesa);

      Pedido::modificarPrecioTotal($precioTotal, $pedido->pedidoId);

      return $response;
    }

    public function TraerTiempoDeEspera($request, $response, $args)
    {
      if(isset($_GET['codigoDeMesa']) && isset($_GET['codigoPedido'])){

        $codigoDeMesa = $_GET['codigoDeMesa'];
        $codigoPedido = $_GET['codigoPedido'];
        
        $retorno = Pedido::SacarTiempoDemora($codigoPedido);
        $payload = json_encode(array("tiempoDeEspera" => $retorno));
      }
      else{
        $payload = json_encode("Ingrese el codigo de mesa y el pedido");
      }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
      // Buscamos mesa por codigoDeMesa
      $m = $args['codigoDeMesa'];
      $mesa = Mesa::obtenerMesa($m);

      $payload = json_encode($mesa);

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Mesa::obtenerTodos();
      $payload = json_encode(array("listaMesa" => $lista));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigoDeMesa = $parametros['codigoDeMesa'];

        if(NULL != (Mesa::obtenerMesa($codigoDeMesa))){

          $mesa = Mesa::obtenerMesa($codigoDeMesa);

          if(($parametros['estadoDeMesa'] != "cerrada" && $mesa->estadoDeMesa == "cerrada") || NULL == $mesa->horaInicio){
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $horaInicio = date("Y-m-d H:i:s");
          }
          else{
            $horaInicio = $mesa->horaInicio;
          }

          if(isset($parametros['estadoDeMesa'])){
            $estadoDeMesa = $parametros['estadoDeMesa'];
          }
          else{
            $estadoDeMesa = $mesa->estadoDeMesa;
          }

          if(isset($parametros['codigoPedido'])){
            $codigoPedido = $parametros['codigoPedido'];
          }
          else{
            $codigoPedido = $mesa->codigoPedido;
          }

          if(isset($_FILES["foto"]["name"])){
            $foto = $_FILES["foto"]["name"];
          }
          else{
            $foto = $mesa->foto;
          }

          if($estadoDeMesa == "cerrada"){
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $horaFin = date("Y-m-d H:i:s");
          }
          else{
            $horaFin = NULL;
          }


          Mesa::modificarMesa($codigoDeMesa, $estadoDeMesa, $codigoPedido, $foto, $horaInicio, $horaFin);
  
          $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));  
        }
        else{
          $payload = json_encode(array("mensaje" => "codigoDeMesa incorrecto"));
        }

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
