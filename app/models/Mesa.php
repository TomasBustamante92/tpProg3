<?php

class Mesa
{
    public $MesaId;
    public $codigoDeMesa;
    public $estadoDeMesa;
    public $codigoPedido;
    public $foto;

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (codigoDeMesa, estadoDeMesa, codigoPedido, foto) VALUES (:codigoDeMesa, :estadoDeMesa, :codigoPedido, :foto)");
        $consulta->bindValue(':codigoDeMesa', $this->codigoDeMesa, PDO::PARAM_STR);
        $consulta->bindValue(':estadoDeMesa', $this->estadoDeMesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT MesaId, codigoDeMesa, estadoDeMesa, codigoPedido, foto FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesa($codigoDeMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT MesaId, codigoDeMesa, estadoDeMesa, codigoPedido, foto FROM mesas WHERE codigoDeMesa = :codigoDeMesa");
        $consulta->bindValue(':codigoDeMesa', $codigoDeMesa, PDO::PARAM_STR);
        $consulta->execute();
        
        return $consulta->fetchObject('Mesa');
    }

    public static function modificarMesa($codigoDeMesa, $estadoDeMesa, $codigoPedido,$foto)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estadoDeMesa = :estadoDeMesa, codigoPedido = :codigoPedido, foto = :foto WHERE codigoDeMesa = :codigoDeMesa");
        $consulta->bindValue(':codigoDeMesa', $codigoDeMesa, PDO::PARAM_STR);
        $consulta->bindValue(':estadoDeMesa', $estadoDeMesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $foto, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
}