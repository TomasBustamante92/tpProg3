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
require_once './middlewares/SocioMiddleware.php';
require_once './middlewares/MozoMiddleware.php';



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
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
})->add(new AutentificadorSocioMiddleware());

// PEDIDOS
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('/{pedidoId}', \PedidoController::class . ':TraerUno')->add(new AutentificadorSocioMiddleware());
  $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(new AutentificadorSocioMiddleware());
  $group->post('[/]', \PedidoController::class . ':CargarUno')->add(new AutentificadorMozoMiddleware());
});

// PEDIDO INDIVIDUAL
$app->group('/pedidoIndividuales', function (RouteCollectorProxy $group) {
  $group->post('/modificar', \PedidoIndividualController::class . ':ModificarUno')->add(new AutentificadorPersonalMiddleware());
  $group->get('/pedidosAtrasados', \PedidoIndividualController::class . ':TraerAtrasados')->add(new AutentificadorSocioMiddleware());
  $group->get('/pendientes', \PedidoIndividualController::class . ':TraerPendientes')->add(new AutentificadorPersonalMiddleware());
  $group->get('/enPreparacion', \PedidoIndividualController::class . ':TraerEnPreparacion')->add(new AutentificadorPersonalMiddleware());
  $group->get('/listoParaServir', \PedidoIndividualController::class . ':TraerListoParaServir')->add(new AutentificadorMozoMiddleware());
  $group->get('/{pedidoIndividualId}', \PedidoIndividualController::class . ':TraerUno')->add(new AutentificadorSocioMiddleware());
  $group->get('[/]', \PedidoIndividualController::class . ':TraerTodos')->add(new AutentificadorSocioMiddleware());
  $group->post('[/]', \PedidoIndividualController::class . ':CargarUno')->add(new AutentificadorMozoMiddleware());
});

// MESAS
$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('/masUsada', \EncuestaController::class . ':TraerMasUsada')->add(new AutentificadorPersonalMiddleware());
  $group->get('/cobrar', \MesaController::class . ':Cobrar')->add(new AutentificadorMozoMiddleware()); 
  $group->get('/tiempo', \MesaController::class . ':TraerTiempoDeEspera');
  $group->get('[/]', \MesaController::class . ':TraerTodos')->add(new AutentificadorSocioMiddleware());
  $group->post('/modificar', \MesaController::class . ':ModificarUno')->add(new CambiarMesaMiddleware()); 
  $group->post('[/]', \MesaController::class . ':CargarUno')->add(new AutentificadorSocioMiddleware());
  $group->get('/{codigoDeMesa}', \MesaController::class . ':TraerUno')->add(new AutentificadorSocioMiddleware());
});

// PRODUCTOS
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->post('[/]', \ProductoController::class . ':CargarUno')->add(new AutentificadorSocioMiddleware());
});

// ENCUESTAS
$app->group('/encuestas', function (RouteCollectorProxy $group) {
  $group->post('/importar', \EncuestaController::class . ':LeerCsv');
  $group->get('/exportar', \EncuestaController::class . ':CrearCsv');
  $group->get('/mejor', \EncuestaController::class . ':TraerMejor')->add(new AutentificadorSocioMiddleware());
  $group->get('[/]', \EncuestaController::class . ':TraerTodos');
  $group->post('[/]', \EncuestaController::class . ':CargarUno');
});

$app->post('/login', \LoginController::class . ':cargarUsuario');

$app->run();


// php -S localhost:666 -t app

