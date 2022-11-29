<?php

class PedidoIndividual
{
    public $pedidoIndividualId;
    public $pedidoId;
    public $productoId;
    public $estado;
    public $tiempoAprox;
    public $cantidad;
    public $tiempoEntrega;
    public $UsuarioId;


    public function crearPedidoIndividual()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidoindividuales (pedidoId, productoId, estado, tiempoAprox, cantidad) VALUES (:pedidoId, :productoId, :estado, :tiempoAprox, :cantidad)");
        $consulta->bindValue(':pedidoId', $this->pedidoId, PDO::PARAM_STR);
        $consulta->bindValue(':productoId', $this->productoId, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoAprox', $this->tiempoAprox, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    static public function CargarTiempoAprox($tiempoAprox, $tiempoProd)
    {
        $tiempoProd = intval($tiempoProd);
        $array = explode( ':', $tiempoAprox); 
        $horas = intval($array[0]);
        $minutos = intval($array[1]);

        if($tiempoProd < 60){
            $minutos += $tiempoProd;
        }
        else{
            $horas++;
            $tiempoProd -= 60;
            $minutos += $tiempoProd;
        }

        if($minutos > 59){
            $minutos -= 60;
            $horas++;
        }

        if($minutos < 10){
            $retorno = $horas . ":0" . $minutos;
        }
        else{
            $retorno = $horas . ":" . $minutos;
        }

        return $retorno;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoindividuales.pedidoIndividualId, pedidoindividuales.pedidoId, pedidoindividuales.productoId, pedidoindividuales.estado, pedidoindividuales.tiempoAprox, productos.nombre, productos.tipoUsuario, pedidoindividuales.cantidad, pedidoindividuales.UsuarioId FROM pedidoindividuales INNER JOIN productos ON pedidoindividuales.productoId = productos.ProductoId");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoIndividual');
    }

    public static function obtenerPendientes($tipo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoindividuales.pedidoIndividualId, pedidoindividuales.pedidoId, pedidoindividuales.productoId, pedidoindividuales.estado, pedidoindividuales.tiempoAprox, productos.nombre, productos.tipoUsuario, pedidoindividuales.cantidad, pedidoindividuales.UsuarioId FROM pedidoindividuales INNER JOIN productos ON pedidoindividuales.productoId = productos.ProductoId WHERE productos.tipoUsuario = :tipo AND pedidoindividuales.estado = 'pendiente'");
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);        
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoIndividual');
    }

    public static function obtenerEnPreparacion($UsuarioId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoindividuales.pedidoIndividualId, pedidoindividuales.pedidoId, pedidoindividuales.productoId, pedidoindividuales.estado, pedidoindividuales.tiempoAprox, productos.nombre, productos.tipoUsuario, pedidoindividuales.cantidad, pedidoindividuales.UsuarioId FROM pedidoindividuales INNER JOIN productos ON pedidoindividuales.productoId = productos.ProductoId INNER JOIN usuarios ON pedidoindividuales.UsuarioId = usuarios.UsuarioId WHERE pedidoindividuales.estado = 'en preparacion' AND usuarios.UsuarioId = :UsuarioId");
        $consulta->bindValue(':UsuarioId', $UsuarioId, PDO::PARAM_STR);        
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoIndividual');
    }

    public static function obtenerListoParaServir()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoindividuales.pedidoIndividualId, pedidoindividuales.pedidoId, pedidoindividuales.productoId, pedidoindividuales.tiempoEntrega,  pedidoindividuales.estado, pedidoindividuales.tiempoAprox, productos.nombre, productos.tipoUsuario, pedidoindividuales.cantidad, pedidoindividuales.UsuarioId FROM pedidoindividuales INNER JOIN productos ON pedidoindividuales.productoId = productos.ProductoId INNER JOIN usuarios ON pedidoindividuales.UsuarioId = usuarios.UsuarioId WHERE pedidoindividuales.estado = 'listo para servir'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoIndividual');
    }

    public static function obtenerPorPedido($pedidoId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoindividuales.pedidoIndividualId, pedidoindividuales.pedidoId, pedidoindividuales.productoId, pedidoindividuales.estado, pedidoindividuales.tiempoAprox, pedidoindividuales.tiempoEntrega, productos.nombre, productos.tipoUsuario, pedidoindividuales.cantidad, pedidoindividuales.UsuarioId FROM pedidoindividuales INNER JOIN productos ON pedidoindividuales.productoId = productos.ProductoId WHERE pedidoindividuales.pedidoId = :pedidoId AND pedidoindividuales.estado != 'cancelado'");
        $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoIndividual');
    }

    public static function obtenerPedido($pedidoIndividualId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoindividuales.pedidoIndividualId, pedidoindividuales.pedidoId, pedidoindividuales.productoId, pedidoindividuales.estado, pedidoindividuales.tiempoAprox, productos.nombre, productos.tipoUsuario FROM pedidoindividuales INNER JOIN productos ON pedidoindividuales.productoId = productos.ProductoId WHERE pedidoIndividualId = :pedidoIndividualId");
        $consulta->bindValue(':pedidoIndividualId', $pedidoIndividualId, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('PedidoIndividual');
    }

    public static function obtenerPedidoPorCodigo($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoindividuales.pedidoIndividualId, pedidoindividuales.tiempoAprox, pedidos.codigoPedido FROM pedidoindividuales INNER JOIN pedidos ON pedidos.pedidoId = pedidoindividuales.pedidoId WHERE pedidos.codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoIndividual');
    }

    public static function modificarPedido($pedidoIndividualId, $estado, $tiempoEntrega, $UsuarioId)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidoindividuales SET estado = :estado, tiempoEntrega = :tiempoEntrega, UsuarioId = :UsuarioId WHERE pedidoIndividualId = :pedidoIndividualId");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEntrega', $tiempoEntrega, PDO::PARAM_INT);
        $consulta->bindValue(':pedidoIndividualId', $pedidoIndividualId, PDO::PARAM_INT);
        $consulta->bindValue(':UsuarioId', $UsuarioId, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarPedido($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidoindividuales SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
}