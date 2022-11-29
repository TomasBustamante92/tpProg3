<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      
      $nombre = $parametros['nombre'];
      $tipoUsuario = $parametros['tipoUsuario'];
      $precio = $parametros['precio'];
      $stock = $parametros['stock'];
      $tiempoDePreparacion = $parametros['tiempoDePreparacion'];


      // Creamos el producto
      $p = new Producto();
      $p->nombre = $nombre;
      $p->tipoUsuario = $tipoUsuario;
      $p->precio = $precio;
      $p->stock = $stock;
      $p->tiempoDePreparacion = $tiempoDePreparacion;
      $p->crearProducto();

      $payload = json_encode(array("mensaje" => "Producto " . $p->nombre . " creado con exito"));
    
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $ProductoId = $args['ProductoId'];
        $producto = Producto::obtenerProducto($ProductoId);
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));

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
