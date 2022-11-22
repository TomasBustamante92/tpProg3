<?php
require_once './models/PedidoIndividual.php';
require_once './interfaces/IApiUsable.php';

class PedidoIndividualController extends PedidoIndividual implements IApiUsable
{
//   function SumarProductor($productos){
//     $total = 0;
//     foreach($productos as $ps){
//       $producto = Producto::obtenerProducto($ps);
//       $total += PedidoController::ParsearProductoPrecio($producto->ProductoId);
//     }
//     return $total;
//   }

//   function SeleccionarProductoMasLento($productos){
//     $masLento = 0;
//     foreach($productos as $ps){
//       $producto = Producto::obtenerProducto($ps);
//       $masLentoAux = PedidoController::ParsearProductoMasLento($producto->ProductoId);
//       if($masLento < $masLentoAux){
//         $masLento = $masLentoAux;
//       }
//     }
//     return $masLento;
//   }

  public function CargarUno($request, $response, $args)
  {
  }

  public function TraerUno($request, $response, $args)
  {
    $pedidoIndividualId = $args['pedidoIndividualId'];
    $pedido = PedidoIndividual::obtenerPedido($pedidoIndividualId);
    $payload = json_encode($pedido);

    $response->getBody()->write($payload);
    return $response
    ->withHeader('Content-Type', 'application/json');
  }

//   public function TiempoDeEspera($request, $response, $args)
//   {
//       $pedido = $args['codigoPedido'];

//       $codigoPedido = Pedido::obtenerPedido($pedido);
      
//       $codigoPedido = (object) array_merge( (array)$codigoPedido, array( 'pedido' => PedidoController::ParsearProductoNombre($codigoPedido->ProductoId)));
//       unset($codigoPedido->ProductoId);
//       $payload = json_encode($codigoPedido);

//       // $payload = "";
//       // $payload .= "Codigo del pedido: " . $codigoPedido->codigoPedido . "\n";
//       // $payload .= "Nombre del cliente: " . $codigoPedido->nombreDelCliente . "\n";
//       // $payload .= "Pedido: " . PedidoController::ParsearProductoNombre($codigoPedido->ProductoId); 
//       // $payload .= "Estado: " . $codigoPedido->estado . "\n";
//       // $payload .= "Foto: " . $codigoPedido->foto . "\n";
//       // $payload .= "Tiempo de preparacion: " . $codigoPedido->tiempoDePreparacion . " min \n";
//       // $payload .= "Precio total: $" . $codigoPedido->precioTotal . "\n\n";
        
//       $response->getBody()->write($payload);
//       return $response
//         ->withHeader('Content-Type', 'application/json');
//   }
    
//   function ParsearProductoMasLento($ProductoId){
//     $array = explode( ',', $ProductoId); 
//     $masLento = 0;
//     foreach($array as $a){
//       $producto = Producto::obtenerProducto($a)->tiempoDePreparacion;
//       if($masLento < $producto){
//         $masLento = $producto;
//       }
//     }  
//     return $masLento;
//   }

//   function ParsearProductoPrecio($ProductoId){
//     $array = explode( ',', $ProductoId); 
//     $total = 0;
//     foreach($array as $a){
//       $total += Producto::obtenerProducto($a)->precio;
//     }      
//     return $total;
//   }

//   function ParsearProductoNombre($ProductoId){
//     $array = explode( ',', $ProductoId); 
//     $retorno = "";
//     for($i=0 ; $i<sizeof($array) ; $i++){
//       $retorno .= Producto::obtenerProducto($array[$i])->nombre;
//       if($i != (sizeof($array) - 1)){
//         $retorno .= ", ";
//       }
//     }      
//     return $retorno;
//   }

  public function TraerTodos($request, $response, $args)
  {
      $lista = PedidoIndividual::obtenerTodos();
      $payload = json_encode($lista);

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function TraerPendientes($request, $response, $args)
  {
      $parametros = $request->getParsedBody();
      $params = (array)$request->getParsedBody();
      $tipo = $params['tipo'];

      if($tipo != "mozo"){
        $pedidos = PedidoIndividual::obtenerPendientes($tipo);

        if(!empty($pedidos)){
          $payload = json_encode($pedidos);
        }
        else{
          $mensaje = "No hay pedidos para " . $tipo;
          $payload = json_encode(array("mensaje" => $mensaje));
        }  
      }
      else{
        $mensaje = "Los " . $tipo . " no tienen acceso a este pedido";
        $payload = json_encode(array("mensaje" => $mensaje));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }
  
  public function ModificarUno($request, $response, $args)
  {
      $parametros = $request->getParsedBody();

      $pedidoIndividualId = $parametros['pedidoIndividualId'];
      $estado = $parametros['estado'];

      if(isset($parametros['tiempoDePreparacion'])){
        $tiempoDePreparacion = $parametros['tiempoDePreparacion'];
      }
      else{
        $tiempoDePreparacion = 0;
      }

      $params = (array)$request->getParsedBody();
      $tipo = $params['tipo'];
      $pedido = PedidoIndividual::obtenerPedido($pedidoIndividualId);

      if($pedido->tipoUsuario == $tipo){
        if(NULL != PedidoIndividual::obtenerPedido($pedidoIndividualId)){
        
          PedidoIndividual::modificarPedido($pedidoIndividualId,$estado,$tiempoDePreparacion);

          Pedido::ActualizarPedido($pedido->pedidoId);
  
          $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        }
        else{
          $payload = json_encode(array("mensaje" => "pedidoIndividualId inexistente"));
        }
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
