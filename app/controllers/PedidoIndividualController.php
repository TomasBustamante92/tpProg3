<?php
require_once './models/PedidoIndividual.php';
require_once './interfaces/IApiUsable.php';

class PedidoIndividualController extends PedidoIndividual implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
      
    $pedidoId = $parametros['pedidoId'];
    $productoId = $parametros['productoId'];
    $cantidad = $parametros['cantidad'];
    
    $prodAux = Producto::obtenerProducto($productoId);

    if($prodAux->stock > $cantidad){

      Producto::actualizarStock($productoId, $prodAux->stock - intval($cantidad));
      
      date_default_timezone_set('America/Argentina/Buenos_Aires');
      $tiempoAprox = PedidoIndividual::CargarTiempoAprox(date("H:i"), $prodAux->tiempoDePreparacion);
      
      // Creamos el pedidoIndividual
      $p = new PedidoIndividual();
      $p->pedidoId = $pedidoId;
      $p->productoId = $productoId;
      $p->tiempoAprox = $tiempoAprox;
      $p->cantidad = $cantidad;
      $p->estado = "pendiente";
      $p->crearPedidoIndividual();

      $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
    }
    else{
      $payload = json_encode(array("mensaje" => "No hay stock suficiente"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $pedidoIndividualId = $args['pedidoIndividualId'];
    $payload = PedidoIndividual::obtenerPedido($pedidoIndividualId);

    $response->getBody()->write(json_encode($payload));
    return $response
    ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

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

    $pedidos = PedidoIndividual::obtenerPendientes($tipo);

    if(!empty($pedidos)){
      $payload = json_encode($pedidos);
    }
    else{
      $mensaje = "No hay pedidos pendientes para " . $tipo;
      $payload = json_encode(array("mensaje" => $mensaje));
    }  

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  public function TraerListoParaServir($request, $response, $args)
  {
      $parametros = $request->getParsedBody();
      $params = (array)$request->getParsedBody();
        
      $pedidos = PedidoIndividual::obtenerListoParaServir();

      if(!empty($pedidos)){
        $payload = json_encode($pedidos);
      }
      else{
        $mensaje = "No hay pedidos listos para servir";
        $payload = json_encode(array("mensaje" => $mensaje));
      }  

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public static function TraerAtrasados($request, $response, $args)
  {
      // $parametros = $request->getParsedBody();
      // $pedidos = PedidoIndividual::obtenerTodos();
      // $pedidosTarde = [];

      // foreach($pedidos as $p){
      //   $horaAprox = explode(":", $p->tiempoAprox);
      //   $horaEntrega = explode(":", $p->tiempoEntrega);

      //   echo $horaAprox[0];
        // $horaAproxHora = intval($horaAprox[0]);
        // $horaAproxMin = intval($horaAprox[1]);
        // $horaEntregaHora = intval($horaEntrega[0]);
        // $horaEntregaMin = intval($horaEntrega[1]);

        // if($horaAproxHora < $horaEntregaHora){
        //   $pedidosTarde[] = $p;
        // }
        // else if($horaAproxHora == $horaEntregaHora && $horaAproxMin < $horaEntregaMin){
        //   $pedidosTarde[] = $p;
        // }
      // }

      // $response->getBody()->write(json_encode($pedidosTarde));
      // return $response
      //   ->withHeader('Content-Type', 'application/json');
  }

  public function TraerEnPreparacion($request, $response, $args)
  {
      $parametros = $request->getParsedBody();
      $params = (array)$request->getParsedBody();
      $tipo = $params['tipo'];
      $UsuarioId = $params['UsuarioId'];

      $pedidos = PedidoIndividual::obtenerEnPreparacion($UsuarioId);

      if(!empty($pedidos)){
        $payload = json_encode($pedidos);
      }
      else{
        $mensaje = "No hay pedidos en preparacion para " . $tipo;
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

      $params = (array)$request->getParsedBody();
      $tipo = $params['tipo'];
      $UsuarioId = $params['UsuarioId'];
      $pedido = PedidoIndividual::obtenerPedido($pedidoIndividualId);
      
      if(NULL != PedidoIndividual::obtenerPedido($pedidoIndividualId)){
        if($pedido->tipoUsuario == $tipo){
        
          if($estado == "cancelado"){
            $payload = json_encode(array("mensaje" => "Los " . $tipo . " no pueden cancelar pedidos"));
          }
          else{

            if($estado != "listo para servir"){
              $tiempoEntrega = $pedido->tiempoEntrega;
            }
            else{
              $tiempoEntrega = $params['tiempoEntrega'];
            }

            PedidoIndividual::modificarPedido($pedidoIndividualId, $estado, $tiempoEntrega, $UsuarioId);
            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
          }

        }
        else if($estado == "cancelado" && $tipo == "socio" ){

          $tiempoEntrega = $pedido->tiempoEntrega;
          PedidoIndividual::modificarPedido($pedidoIndividualId, $estado, $tiempoEntrega, $UsuarioId);
  
          $payload = json_encode(array("mensaje" => "Pedido cancelado con exito"));
        }
        else if($tipo == "mozo" && $estado == "entregado"){
          
          $tiempoEntrega = $pedido->tiempoEntrega;
          PedidoIndividual::modificarPedido($pedidoIndividualId, $estado, $tiempoEntrega, $UsuarioId);
  
          $payload = json_encode(array("mensaje" => "Pedido entregado con exito"));
        }
        else{
          $mensaje = "Los " . $tipo . " no tienen acceso a este pedido";
          $payload = json_encode(array("mensaje" => $mensaje));
        }
      }
      else{
        $payload = json_encode(array("mensaje" => "pedidoIndividualId inexistente"));
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



