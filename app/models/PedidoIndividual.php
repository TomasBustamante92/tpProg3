<?php

class PedidoIndividual
{
    public $pedidoIndividualId;
    public $pedidoId;
    public $productoId;
    public $estado;
    public $tiempoDePreparacion;

    public static function crearPedidoIndividual($estado, $productoId, $tiempoDePreparacion, $pedidoId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidoindividuales (pedidoId, estado, productoId, tiempoDePreparacion) VALUES (:pedidoId, :estado, :productoId, :tiempoDePreparacion)");
        $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':productoId', $productoId, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoDePreparacion', $tiempoDePreparacion, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoindividuales.pedidoIndividualId, pedidoindividuales.pedidoId, pedidoindividuales.productoId, pedidoindividuales.estado, pedidoindividuales.tiempoDePreparacion, productos.nombre, productos.tipoUsuario FROM pedidoindividuales INNER JOIN productos ON pedidoindividuales.productoId = productos.ProductoId");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoIndividual');
    }

    public static function obtenerPendientes($tipo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoindividuales.pedidoIndividualId, pedidoindividuales.pedidoId, pedidoindividuales.productoId, pedidoindividuales.estado, pedidoindividuales.tiempoDePreparacion, productos.nombre, productos.tipoUsuario FROM pedidoindividuales INNER JOIN productos ON pedidoindividuales.productoId = productos.ProductoId WHERE productos.tipoUsuario = :tipo AND pedidoindividuales.estado != 'listo para servir'");
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoIndividual');
    }

    public static function obtenerPorPedido($pedidoId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoindividuales.pedidoIndividualId, pedidoindividuales.pedidoId, pedidoindividuales.productoId, pedidoindividuales.estado, pedidoindividuales.tiempoDePreparacion, productos.nombre, productos.tipoUsuario FROM pedidoindividuales INNER JOIN productos ON pedidoindividuales.productoId = productos.ProductoId WHERE pedidoindividuales.pedidoId = :pedidoId");
        $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoIndividual');
    }

    public static function obtenerPedido($pedidoIndividualId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoindividuales.pedidoIndividualId, pedidoindividuales.pedidoId, pedidoindividuales.productoId, pedidoindividuales.estado, pedidoindividuales.tiempoDePreparacion, productos.nombre, productos.tipoUsuario FROM pedidoindividuales INNER JOIN productos ON pedidoindividuales.productoId = productos.ProductoId WHERE pedidoIndividualId = :pedidoIndividualId");
        $consulta->bindValue(':pedidoIndividualId', $pedidoIndividualId, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('PedidoIndividual');
    }

    public static function modificarPedido($pedidoIndividualId, $estado, $tiempoDePreparacion)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidoindividuales SET estado = :estado, tiempoDePreparacion = :tiempoDePreparacion WHERE pedidoIndividualId = :pedidoIndividualId");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoDePreparacion', $tiempoDePreparacion, PDO::PARAM_INT);
        $consulta->bindValue(':pedidoIndividualId', $pedidoIndividualId, PDO::PARAM_INT);
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