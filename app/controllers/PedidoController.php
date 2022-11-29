<?php

use function PHPSTORM_META\type;

require_once './models/Pedido.php';
require_once './models/PedidoIndividual.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
  function SumarProductor($productos){
    $total = 0;
    foreach($productos as $ps){
      $producto = Producto::obtenerProducto($ps);
      $total += PedidoController::ParsearProductoPrecio($producto->ProductoId);
    }
    return $total;
  }

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigoPedido = $parametros['codigoPedido'];
    $nombreDelCliente = $parametros['nombreDelCliente'];

    // Creamos el pedido
    $p = new Pedido();
    $p->codigoPedido = $codigoPedido;
    $p->nombreDelCliente = $nombreDelCliente;
    $pedidoId = $p->crearPedido();

    $payload = json_encode(array("mensaje" => "Pedido " . $codigoPedido . " creado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $pedidoId = $args['pedidoId'];
    $pedido = Pedido::obtenerPedido($pedidoId);

    $minutoFinal = Pedido::SacarTiempoDemora($pedido->codigoPedido);

    $pedido = (object) array_merge( (array)$pedido, array('tiempoDemora' => $minutoFinal));  
    $pedido = (object) array_merge( (array)$pedido, array('detalle' => PedidoIndividual::obtenerPorPedido($pedido->pedidoId)));

    $response->getBody()->write(json_encode($pedido));
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  function ParsearProductoMasLento($ProductoId){
    $array = explode( ',', $ProductoId);
    $masLento = 0;
    foreach($array as $a){
      $producto = Producto::obtenerProducto($a)->tiempoDePreparacion;
      if($masLento < $producto){
        $masLento = $producto;
      }
    }
    return $masLento;
  }

  function ParsearProductoPrecio($ProductoId){
    $array = explode( ',', $ProductoId);
    $total = 0;
    foreach($array as $a){
      $total += Producto::obtenerProducto($a)->precio;
    }
    return $total;
  }


  public function TraerTodos($request, $response, $args)
  {
      $lista = Pedido::obtenerTodos();

      for($i=0 ; $i<sizeof($lista) ; $i++){
        $lista[$i] = (object) array_merge( (array)$lista[$i], array( 'detalle' => PedidoIndividual::obtenerPorPedido($lista[$i]->pedidoId)));
      }
      $payload = json_encode($lista);

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }


  public function ModificarUno($request, $response, $args)
  {
      $parametros = $request->getParsedBody();

      $pedidoId = $parametros['pedidoId'];
      $foto = $_FILES["foto"]["name"];

      $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

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
