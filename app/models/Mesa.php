<?php
require_once './models/fpdf.php';

class Mesa
{
    public $MesaId;
    public $codigoDeMesa;
    public $estadoDeMesa;
    public $codigoPedido;
    public $foto;
    public $horaInicio;
    public $horaFin;

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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT MesaId, codigoDeMesa, estadoDeMesa, codigoPedido, foto, horaInicio, horaFin FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerCuenta($codigoDeMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT productos.precio, productos.ProductoId, productos.nombre, pedidoindividuales.cantidad, pedidoindividuales.estado, pedidoindividuales.pedidoId, pedidos.codigoPedido FROM productos INNER JOIN pedidoindividuales ON pedidoindividuales.productoId = productos.ProductoId INNER JOIN pedidos ON pedidos.pedidoId = pedidoindividuales.pedidoId INNER JOIN mesas ON mesas.codigoPedido = pedidos.codigoPedido WHERE mesas.codigoDeMesa = :codigoDeMesa AND pedidoindividuales.estado != 'cancelado'");
        $consulta->bindValue(':codigoDeMesa', $codigoDeMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoIndividual');
    }

    public static function obtenerMesa($codigoDeMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT MesaId, codigoDeMesa, estadoDeMesa, codigoPedido, foto, horaInicio, horaFin FROM mesas WHERE codigoDeMesa = :codigoDeMesa");
        $consulta->bindValue(':codigoDeMesa', $codigoDeMesa, PDO::PARAM_STR);
        $consulta->execute();
        
        return $consulta->fetchObject('Mesa');
    }

    public static function modificarMesa($codigoDeMesa, $estadoDeMesa, $codigoPedido, $foto, $horaInicio, $horaFin)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estadoDeMesa = :estadoDeMesa, codigoPedido = :codigoPedido, foto = :foto, horaInicio = :horaInicio, horaFin = :horaFin WHERE codigoDeMesa = :codigoDeMesa");
        $consulta->bindValue(':codigoDeMesa', $codigoDeMesa, PDO::PARAM_STR);
        $consulta->bindValue(':estadoDeMesa', $estadoDeMesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $foto, PDO::PARAM_STR);
        $consulta->bindValue(':horaInicio', $horaInicio, PDO::PARAM_STR);
        $consulta->bindValue(':horaFin', $horaFin, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function CalcularPrecioTotal($codigoDeMesa){

      $mesaAux = Mesa::obtenerCuenta($codigoDeMesa);
      $respuesta = [];
      
      $precioTotal = 0;

      for($i=0 ; $i < sizeof($mesaAux) +2 ; $i++){
        
        if($i < sizeof($mesaAux)){

          $precioTotal += ($mesaAux[$i]->precio * $mesaAux[$i]->cantidad);
        }
      }

      return $precioTotal;
    }

    public static function CrearPdf($codigoDeMesa){
        $pdf = new PDF();
        $mesaAux = Mesa::obtenerCuenta($codigoDeMesa);
        $respuesta = [];
        
        $precioTotal = 0;

        for($i=0 ; $i < sizeof($mesaAux) +2 ; $i++){
          if($i == 0){
            $respuesta[]["cantidad"] = "test";
          }
          
          if($i < sizeof($mesaAux)){
            $respuesta[$i]["cantidad"] = $mesaAux[$i]->cantidad;
            $respuesta[$i]["detalle"] = $mesaAux[$i]->nombre;
            $respuesta[$i]["precio"] = ($mesaAux[$i]->precio * $mesaAux[$i]->cantidad);
            $precioTotal += ($mesaAux[$i]->precio * $mesaAux[$i]->cantidad);
          }
          else if($i == sizeof($mesaAux)){
            $respuesta[$i]["cantidad"] = "";
            $respuesta[$i]["detalle"] = "";
            $respuesta[$i]["precio"] = "";
          }
          else{
            $respuesta[$i]["cantidad"] = "";
            $respuesta[$i]["detalle"] = "Total:";
            $respuesta[$i]["precio"] = $precioTotal;
          }
        }

        if(sizeof($respuesta) == 0)
        {
          $pdf->CargarMensaje("No hay registros para imprimir");
        }
        else
        {
          $pdf->CargarTabla($respuesta);
        }
        
        $pdf->ImprimirPdf("cuenta.pdf"); 

        return $respuesta;
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