<?php
require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';

class EncuestaController extends Encuesta implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

        $codigoDeMesa = $parametros['codigoDeMesa'];
        $puntuacionMesa = $parametros['puntuacionMesa'];
        $puntuacionRestaurant = $parametros['puntuacionRestaurant'];
        $puntuacionMozo = $parametros['puntuacionMozo'];
        $puntuacionCocinero = $parametros['puntuacionCocinero'];
        $encuesta = $parametros['encuesta'];


        // Creamos la encuesta
        $m = new Encuesta();
        $m->codigoDeMesa = $codigoDeMesa;
        $m->puntuacionMesa = $puntuacionMesa;
        $m->puntuacionRestaurant = $puntuacionRestaurant;
        $m->puntuacionMozo = $puntuacionMozo;
        $m->puntuacionCocinero = $puntuacionCocinero;
        $m->encuesta = $encuesta;
        $m->crearEncuesta();

        $payload = json_encode(array("mensaje" => "Encuesta creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos encuesta por codigoDeMesa
        $cod = $args['codigoDeMesa'];
        $encuesta = Encuesta::obtenerEncuesta($cod);
        $payload = json_encode($encuesta);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function LeerCsv($request, $response, $args)
    {
      $csv = $_FILES["csv"]["tmp_name"];
      $archivo = fopen($csv,"r");

      while(!feof($archivo))
      {
        $aux = fgets($archivo);
        $arrayEnc = explode( ',', $aux);

        if($arrayEnc[0] != "encuestaId"){
          $encuestaAux = new Encuesta();
          $encuestaAux->encuestaId = $arrayEnc[0];
          $encuestaAux->codigoDeMesa = $arrayEnc[1];
          $encuestaAux->puntuacionMesa = $arrayEnc[2];
          $encuestaAux->puntuacionRestaurant = $arrayEnc[3];
          $encuestaAux->puntuacionMozo = $arrayEnc[4];
          $encuestaAux->puntuacionCocinero = $arrayEnc[5];
          $encuestaAux->encuesta = preg_replace('/[0-9\@\.\;\n]+/', '', $arrayEnc[6]);
          $encuestaAux->crearEncuesta();
          $encuestas[] = $encuestaAux;
        }
      }
      fclose($archivo);

      $payload = json_encode(array("agregados" => $encuestas));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CrearCsv($request, $response, $args)
    {
      $encuestas = Encuesta::obtenerTodos();

      $retorno = "encuestaId,codigoDeMesa,puntuacionMesa,puntuacionRestaurant,puntuacionMozo,puntuacionCocinero,encuesta\n";

      foreach($encuestas as $e){
        $retorno .= $e->encuestaId . "," . $e->codigoDeMesa . "," . $e->puntuacionMesa . "," . $e->puntuacionRestaurant . "," . $e->puntuacionMozo . "," . $e->puntuacionCocinero . "," . $e->encuesta . "\n";
      }

      $archivo = fopen("encuestas.csv","w");
      fwrite($archivo, $retorno);
      fclose($archivo);
      
      $file = 'encuestas.csv';

      if (file_exists($file)) {
          header('Content-Description: File Transfer');
          header('Content-Type: application/octet-stream');
          header('Content-Disposition: attachment; filename="'.basename($file));
          header('Expires: 0');
          header('Cache-Control: must-revalidate');
          header('Pragma: public');
          header('Content-Length: ' . filesize($file));
          readfile($file);
          exit;
      }

      $payload = ($retorno);

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMasUsada($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $params = (array)$request->getParsedBody();
      $tipo = $params['tipo'];

      if($tipo == "socio"){
        $mesas = Mesa::obtenerTodos();
        $lista = Encuesta::obtenerTodos();
        $masUsadaMesa = "";
        $masUsadaContador = "";
        $masUsada = 0;

        $payload = "";


        for($i=0 ; $i<sizeof($mesas) ; $i++){
          $contador = 0;

          foreach($lista as $l){
            if($l->codigoDeMesa == $mesas[$i]->codigoDeMesa){

              $contador++;
            }
          }

          if($contador > $masUsadaContador){
            $masUsadaContador = $contador;
            $masUsadaMesa = $i;
          }
        }

        $payload = json_encode(array("codigoDeMesa" => $mesas[$masUsadaMesa]->codigoDeMesa, "usada" => $masUsadaContador));

      }
      else{
        $mensaje = "Los " . $tipo . " no tienen acceso a este pedido";
        $payload = json_encode(array("mensaje" => $mensaje));
      }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMejor($request, $response, $args)
    {
        $lista = Encuesta::obtenerTodos();
        $mejoresCom = [];

        foreach($lista as $l){
          $puntajeAux = ($l->puntuacionMesa + $l->puntuacionRestaurant + $l->puntuacionMozo + $l->puntuacionCocinero ) / 4;

          if($puntajeAux > 7){
            $mejoresCom[] = $l;
          }
        }

        $payload = json_encode(array("mejoresPuntajes" => $mejoresCom));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Encuesta::obtenerTodos();
        $payload = json_encode(array("listaEncuesta" => $lista));

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
