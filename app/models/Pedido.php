<?php

class Pedido
{
    public $pedidoId;
    public $codigoPedido;
    public $nombreDelCliente;
    public $precioTotal;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigoPedido, nombreDelCliente) VALUES (:codigoPedido, :nombreDelCliente)");
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':nombreDelCliente', $this->nombreDelCliente, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function SacarTiempoDemora($codigoPedido){

        $pedidos = PedidoIndividual::obtenerPedidoPorCodigo($codigoPedido);
        $retorno = "";
        $horaMayor = -1;
        $minutoMayor = -1;

        foreach($pedidos as $p){
            $tiempoAux = explode( ':', $p->tiempoAprox);
            $dias = intval($tiempoAux[0]);
            $minutos = intval($tiempoAux[1]);

            if($dias > $horaMayor){
                $horaMayor = $dias;
                $minutoMayor = $minutos;
            }
            else if($dias == $horaMayor && $minutos > $minutoMayor){
                $horaMayor = $dias;
                $minutoMayor = $minutos;
            }
        }

        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $horarioActual = date("H:i");
        $horarioActualArr = explode( ':', $horarioActual);
        $horaAct = intval($horarioActualArr[0]);
        $minAct = intval($horarioActualArr[1]);
        $horarioActual = ($horaAct * 60) + $minAct;
        $horarioEstipulado = ($horaMayor * 60) + $minutoMayor;

        $minutoFinal = $horarioEstipulado - $horarioActual;

        if($minutoMayor < 10){
            $minutoMayor = "0" . strval($minutoMayor);
        }

        if($minutoFinal >= 0){
            $retorno .= "El pedido estara a las " . $horaMayor . ":" . $minutoMayor . " - Faltan " . $minutoFinal . " minutos";
        }
        else{
            $minutoFinal *= (-1);
            $retorno .= "El pedido tenia que estar a las " . $horaMayor . ":" . $minutoMayor . " - Se atraso " . $minutoFinal . " minutos";
        }


        return $retorno;
    }


    public static function obtenerPedidoPorId($pedidoId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoId, codigoPedido, nombreDelCliente, precioTotal FROM pedidos WHERE pedidoId = :pedidoId");
        $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoId, codigoPedido, nombreDelCliente, precioTotal FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($pedidoId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidoId, codigoPedido, nombreDelCliente, precioTotal FROM pedidos WHERE pedidoId = :pedidoId");
        $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerIdPorCodigo($codigoDeMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.pedidoId, mesas.estadoDeMesa, mesas.codigoDeMesa, pedidos.codigoPedido FROM pedidos INNER JOIN mesas ON mesas.codigoPedido = pedidos.codigoPedido WHERE mesas.codigoDeMesa = :codigoDeMesa AND mesas.estadoDeMesa != 'cerrada'");
        $consulta->bindValue(':codigoDeMesa', $codigoDeMesa, PDO::PARAM_STR);
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

    public static function modificarPrecioTotal($precioTotal, $pedidoId)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET precioTotal = :precioTotal WHERE pedidoId = :pedidoId");
        $consulta->bindValue(':precioTotal', $precioTotal, PDO::PARAM_STR);
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