<?php
require_once './controllers/AutentificadorJWT.php';

use Illuminate\Support\Arr;
use Psr7Middlewares\Middleware\Payload;
use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
    use Slim\Psr7\Response;

    class CambiarMesaMiddleware{
        
        public function __invoke(Request $request, RequestHandler $handler) : Response
        {
            $response = new Response();

            try{
                $header = $request->getHeaderLine('authorization');
                
                if(!empty($header)){
                    $token = trim(explode("Bearer", $header)[1]);
                    $payload = AutentificadorJWT::ObtenerPayLoad($token);
                    $tipo = $payload->data->tipo;
                    $estadoDeMesa = $_POST["estadoDeMesa"];

                    if($tipo == "socio"){

                        $response = $handler->handle($request);
                    }
                    else if($tipo == "mozo"){

                        if($estadoDeMesa == "cerrada"){
                            $mensaje = "Los mozos no pueden cambiar la mesa a 'cerrada'";
                            $response->getBody()->write($mensaje);
                        }
                        else{
                            $response = $handler->handle($request);
                        }
                    }
                    else{
                        $mensaje = "Acceso denegado";
                        $response->getBody()->write($mensaje);
                    }
                }
                else{
                    $mensaje = "Falta Token";
                    $response->getBody()->write($mensaje);
                }  
            }
            catch (Exception $e){
                $mensaje = json_encode(array("error" => $e->getMessage())); 
                $response->getBody()->write($mensaje);
            }
            
            return $response;
        }
    }
?>