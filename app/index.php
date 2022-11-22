<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './middlewares/CambiarMesaMiddleware.php';
require_once './middlewares/AutentificadorPersonalMiddleware.php';

require_once './controllers/LoginController.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/EncuestaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/PedidoIndividualController.php';


// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// USUARIOS
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  // $group->put('/modificar', \UsuarioController::class . ':ModificarUno');
  // $group->delete('/eliminar', \UsuarioController::class . ':BorrarUno');
  
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
})/*->add(new AutentificadorMiddleware())*/;

// PEDIDOS
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  // $group->delete('/eliminar', \UsuarioController::class . ':BorrarUno');
  
  //$group->get('/{codigoPedido}', \PedidoController::class . ':TiempoDeEspera'); // para el cliente solo tiempo de espera
  //$group->post('/modificar', \PedidoController::class . ':ModificarUno')->add(new AutentificadorPersonalMiddleware());
  $group->get('/{codigoPedido}', \PedidoController::class . ':TraerUno');
  $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(new AutentificadorPersonalMiddleware());
  $group->post('[/]', \PedidoController::class . ':CargarUno')->add(new AutentificadorPersonalMiddleware());
});

// PEDIDO INDIVIDUAL
$app->group('/pedidoIndividuales', function (RouteCollectorProxy $group) {
  // $group->delete('/eliminar', \UsuarioController::class . ':BorrarUno');
  
  $group->get('/pendientes', \PedidoIndividualController::class . ':TraerPendientes')->add(new AutentificadorPersonalMiddleware());
  $group->post('/modificar', \PedidoIndividualController::class . ':ModificarUno')->add(new AutentificadorPersonalMiddleware());
  $group->get('/{pedidoIndividualId}', \PedidoIndividualController::class . ':TraerUno');
  $group->get('[/]', \PedidoIndividualController::class . ':TraerTodos');
  $group->post('[/]', \PedidoIndividualController::class . ':CargarUno');
});

// MESAS
$app->group('/mesas', function (RouteCollectorProxy $group) {

  // $group->delete('/eliminar', \UsuarioController::class . ':BorrarUno');

  $group->get('/tiempo', \MesaController::class . ':TraerTiempoDeEspera');
  $group->post('/modificar', \MesaController::class . ':ModificarUno')->add(new CambiarMesaMiddleware()); 
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/{codigoDeMesa}', \MesaController::class . ':TraerUno');
  $group->post('[/]', \MesaController::class . ':CargarUno')->add(new AutentificadorPersonalMiddleware());
})/*->add(new AutentificadorMiddleware())*/;

// PRODUCTOS
$app->group('/productos', function (RouteCollectorProxy $group) {

  // $group->put('/modificar', \UsuarioController::class . ':ModificarUno');
  // $group->delete('/eliminar', \UsuarioController::class . ':BorrarUno');
  
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{ProductoId}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
})/*->add(new AutentificadorMiddleware())*/;

// ENCUESTAS
$app->group('/encuestas', function (RouteCollectorProxy $group) {
  // $group->put('/modificar', \UsuarioController::class . ':ModificarUno');
  // $group->delete('/eliminar', \UsuarioController::class . ':BorrarUno');
  
  $group->get('[/]', \EncuestaController::class . ':TraerTodos');
  $group->get('/{codigoDeMesa}', \EncuestaController::class . ':TraerUno');
  $group->post('[/]', \EncuestaController::class . ':CargarUno');
})/*->add(new AutentificadorMiddleware())*/;

$app->post('/login', \LoginController::class . ':cargarUsuario');

$app->run();


// php -S localhost:666 -t app

