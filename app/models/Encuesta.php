<?php

class Encuesta
{
    public $encuestaId;
    public $codigoDeMesa;
    public $puntuacionMesa;
    public $puntuacionRestaurant;
    public $puntuacionMozo;
    public $puntuacionCocinero;
    public $encuesta;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (codigoDeMesa, puntuacionMesa,puntuacionRestaurant,puntuacionMozo,puntuacionCocinero,encuesta) VALUES (:codigoDeMesa, :puntuacionMesa, :puntuacionRestaurant, :puntuacionMozo, :puntuacionCocinero, :encuesta)");
        $consulta->bindValue(':codigoDeMesa', $this->codigoDeMesa, PDO::PARAM_STR);
        $consulta->bindValue(':puntuacionMesa', $this->puntuacionMesa, PDO::PARAM_STR);
        $consulta->bindValue(':puntuacionRestaurant', $this->puntuacionRestaurant, PDO::PARAM_STR);
        $consulta->bindValue(':puntuacionMozo', $this->puntuacionMozo, PDO::PARAM_STR);
        $consulta->bindValue(':puntuacionCocinero', $this->puntuacionCocinero, PDO::PARAM_STR);
        $consulta->bindValue(':encuesta', $this->encuesta, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT encuestaId, codigoDeMesa, puntuacionMesa, puntuacionRestaurant, puntuacionMozo, puntuacionCocinero, encuesta FROM encuestas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function obtenerEncuesta($codigoDeMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT encuestaId, codigoDeMesa, puntuacionMesa, puntuacionRestaurant, puntuacionMozo, puntuacionCocinero, encuesta FROM encuestas WHERE codigoDeMesa = :codigoDeMesa");
        $consulta->bindValue(':codigoDeMesa', $codigoDeMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Encuesta');
    }

    public static function modificarUsuario()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave, tipo_perfil = :tipo_perfil WHERE id = :id");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':tipo_perfil', $this->tipo_perfil, PDO::PARAM_STR);
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