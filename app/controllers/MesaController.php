<?php
require_once './models/Mesa.php';
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

    public function TraerTiempoDeEspera($request, $response, $args)
    {
      if(isset($_GET['codigoDeMesa']) && isset($_GET['codigoPedido'])){

        $codigoDeMesa = $_GET['codigoDeMesa'];
        $codigoPedido = $_GET['codigoPedido'];

        $pedido = Pedido::obtenerPedidoPorCodigo($codigoPedido);
        $mensaje = $pedido->tiempoDePreparacion . " minutos";
        $payload = json_encode(array("Tiempo de espera" => $mensaje));
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

        if(NULL != Mesa::obtenerMesa($codigoDeMesa)){
          
          $estadoDeMesa = $parametros['estadoDeMesa'];
          $codigoPedido = $parametros['codigoPedido'];
          $foto = $_FILES["foto"]["name"];
          Mesa::modificarMesa($codigoDeMesa, $estadoDeMesa, $codigoPedido, $foto);
  
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
