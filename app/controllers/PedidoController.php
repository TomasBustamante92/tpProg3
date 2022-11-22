<?php
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

    $params = (array)$request->getParsedBody();
    $tipo = $params['tipo'];
    
    if($tipo == "mozo"){

      $codigoPedido = $parametros['codigoPedido'];
      $nombreDelCliente = $parametros['nombreDelCliente'];
      $ProductoId = $parametros['ProductoId'];
      $estado = $parametros['estado'];
      $productos = explode( ',', $ProductoId); 
      $precioTotal = PedidoController::SumarProductor($productos);   
      

      // Creamos el pedido
      $p = new Pedido();
      $p->codigoPedido = $codigoPedido;
      $p->nombreDelCliente = $nombreDelCliente;
      $p->ProductoId = $ProductoId;
      $p->estado = $estado;
      $p->tiempoDePreparacion = -1;
      $p->precioTotal = $precioTotal;
      $pedidoId = $p->crearPedido();

      // crea pedidos individuales
      for($i=0 ; $i<sizeof($productos) ; $i++){
        PedidoIndividual::crearPedidoIndividual("pendientes",$productos[$i], -1, $pedidoId);
      }

      $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

    }
    else{
      $mensaje = "Los " . $tipo . " no tienen acceso a este pedido";
      $payload = json_encode(array("mensaje" => $mensaje));
    }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
      $pedido = $args['codigoPedido'];
      $codigoPedido = Pedido::obtenerPedido($pedido);
      
      $codigoPedido = (object) array_merge( (array)$codigoPedido, array( 'pedido' => PedidoController::ParsearProductoNombre($codigoPedido->ProductoId)));
      unset($codigoPedido->ProductoId);
      $payload = json_encode($codigoPedido);

      $response->getBody()->write($payload);
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

  function ParsearProductoNombre($ProductoId){
    $array = explode( ',', $ProductoId); 
    $retorno = "";
    for($i=0 ; $i<sizeof($array) ; $i++){
      $retorno .= Producto::obtenerProducto($array[$i])->nombre;
      if($i != (sizeof($array) - 1)){
        $retorno .= ", ";
      }
    }      
    return $retorno;
  }


  public function TraerTodos($request, $response, $args)
  {
      $lista = Pedido::obtenerTodos();
      for($i=0 ; $i<sizeof($lista) ; $i++){
        $lista[$i] = (object) array_merge( (array)$lista[$i], array( 'pedido' => PedidoController::ParsearProductoNombre($lista[$i]->ProductoId)));
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
      
      $params = (array)$request->getParsedBody();
      $tipo = $params['tipo'];
      
      if($tipo == "mozo"){
        //Pedido::modificarPedido($pedidoId, $foto);
        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
      }
      else{
        $mensaje = "Los " . $tipo . " no tienen acceso a este pedido";
        $payload = json_encode(array("mensaje" => $mensaje));
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
