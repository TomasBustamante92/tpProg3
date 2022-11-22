<?php

class Pedido
{
    public $pedidoId;
    public $codigoPedido;
    public $nombreDelCliente;
    public $ProductoId;
    public $estado;
    public $tiempoDePreparacion;
    public $precioTotal;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigoPedido, nombreDelCliente, ProductoId, estado, tiempoDePreparacion, precioTotal) VALUES (:codigoPedido, :nombreDelCliente, :ProductoId, :estado, :tiempoDePreparacion, :precioTotal)");
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':nombreDelCliente', $this->nombreDelCliente, PDO::PARAM_STR);
        $consulta->bindValue(':ProductoId', $this->ProductoId, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoDePreparacion', $this->tiempoDePreparacion, PDO::PARAM_STR); 
        $consulta->bindValue(':precioTotal', $this->precioTotal, PDO::PARAM_STR); 
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function ActualizarPedido($pedidoId){

        $pedido = Pedido::obtenerPedidoPorId($pedidoId);
        $pedidoIndividuales = PedidoIndividual::obtenerPorPedido($pedidoId);
        $pedidoMasLento = -1;
        $faltaParaServir = 0;

        foreach($pedidoIndividuales as $p){

            if($p->tiempoDePreparacion > $pedidoMasLento){
                $pedidoMasLento = $p->tiempoDePreparacion;
            }

            if($p->estado != "listo para servir"){
                $faltaParaServir++;
            }
        }

        Pedido::modificarTiempo($pedidoMasLento, $pedidoId);

        if($faltaParaServir == 0){
            
            Pedido::modificarEstado("listo para servir", $pedidoId);
            Pedido::modificarTiempo(0, $pedidoId);
        }
    }

    public static function obtenerPedidoPorId($pedidoId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoId, codigoPedido, nombreDelCliente, ProductoId, estado, tiempoDePreparacion, precioTotal FROM pedidos WHERE pedidoId = :pedidoId");
        $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerPedidoPorCodigo($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoId, codigoPedido, nombreDelCliente, ProductoId, estado, tiempoDePreparacion, precioTotal FROM pedidos WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoId, codigoPedido, nombreDelCliente, ProductoId, estado, tiempoDePreparacion, precioTotal FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoId, codigoPedido, nombreDelCliente, ProductoId, estado, tiempoDePreparacion, precioTotal FROM pedidos WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function modificarEstado($estado, $pedidoId)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE pedidoId = :pedidoId");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function modificarTiempo($tiempoDePreparacion, $pedidoId)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET tiempoDePreparacion = :tiempoDePreparacion WHERE pedidoId = :pedidoId");
        $consulta->bindValue(':tiempoDePreparacion', $tiempoDePreparacion, PDO::PARAM_STR);
        $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarPedido($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
}