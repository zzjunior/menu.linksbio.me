<?php

declare(strict_types=1);

use App\Controllers\MenuController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\OrderController;
use App\Models\Product;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Report;
use App\Models\StoreSettings;
use App\Models\Store;
use App\Services\TemplateService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\PrintController;
use App\Controllers\ReportController;
use App\Controllers\StoreController;

// Iniciar sessão
session_start();

// Carrega a aplicação
$app = require __DIR__ . '/../config/bootstrap.php';

// Configuração do container
$container = $app->getContainer();

// Registra services no container
$container->set(TemplateService::class, function () {
    return new TemplateService();
});

$container->set(Product::class, function ($container) {
    return new Product($container->get('db'));
});

$container->set(Category::class, function ($container) {
    return new Category($container->get('db'));
});

$container->set(Ingredient::class, function ($container) {
    return new Ingredient($container->get('db'));
});

$container->set(User::class, function ($container) {
    return new User($container->get('db'));
});

$container->set(Order::class, function ($container) {
    return new Order($container->get('db'));
});

$container->set(OrderItem::class, function ($container) {
    return new OrderItem($container->get('db'));
});

$container->set(Customer::class, function ($container) {
    return new Customer($container->get('db'));
});

$container->set(\App\Services\EmailService::class, function () {
    return new \App\Services\EmailService();
});

$container->set(Report::class, function ($container) {
    return new Report($container->get('db'));
});

$container->set(StoreSettings::class, function ($container) {
    return new StoreSettings($container->get('db'));
});

$container->set(Store::class, function ($container) {
    return new Store($container->get('db'));
});

$container->set(\App\Services\SessionService::class, function ($container) {
    return new \App\Services\SessionService($container->get('db'));
});

$container->set(MenuController::class, function ($container) {
    return new MenuController(
        $container->get(Product::class),
        $container->get(Category::class),
        $container->get(Ingredient::class),
        $container->get(User::class),
        $container->get(TemplateService::class)
    );
});

$container->set(AdminController::class, function ($container) {
    return new AdminController(
        $container->get(Product::class),
        $container->get(Category::class),
        $container->get(Ingredient::class),
        $container->get(Order::class),
        $container->get(User::class),
        $container->get(Customer::class),
        $container->get(TemplateService::class)
    );
});

$container->set(AuthController::class, function ($container) {
    return new AuthController(
        $container->get(User::class),
        $container->get(Store::class),
        $container->get(TemplateService::class),
        $container->get(\App\Services\SessionService::class),
        $container->get(\App\Services\EmailService::class)
    );
});

// container com pdo carrinho
$container->set(CartController::class, function ($container) {
    $dbal = $container->get('db'); // Isso é Doctrine\DBAL\Connection
    $pdo = method_exists($dbal, 'getNativeConnection')
        ? $dbal->getNativeConnection()
        : $dbal->getWrappedConnection();
    return new CartController(
        $container->get(Product::class),
        $container->get(Ingredient::class),
        $container->get(Order::class),
        $container->get(OrderItem::class),
        $container->get(User::class),
        $container->get(Customer::class),
        $container->get(StoreSettings::class),
        $container->get(TemplateService::class),
        $pdo 
    );
});

// container com pdo impressão
$container->set(PrintController::class, function ($container) {
    $dbal = $container->get('db');
    $pdo = method_exists($dbal, 'getNativeConnection')
        ? $dbal->getNativeConnection()
        : $dbal->getWrappedConnection();
    return new PrintController(
        $pdo,
        $container->get(User::class),
        //$container->get('logger') // Adicione o logger aqui!
    );
});

// Container para OrderController
$container->set(OrderController::class, function ($container) {
    return new OrderController(
        $container->get(Order::class),
        $container->get(TemplateService::class)
    );
});

// Container para ReportController
$container->set(ReportController::class, function ($container) {
    return new ReportController(
        $container->get(Report::class),
        $container->get(User::class),
        $container->get(TemplateService::class)
    );
});

// Container para StoreController
$container->set(StoreController::class, function ($container) {
    return new StoreController(
        $container->get(StoreSettings::class),
        $container->get(TemplateService::class)
    );
});



// === ROTAS DE AUTENTICAÇÃO ===
$app->get('/admin/login', [AuthController::class, 'loginForm']);
$app->post('/admin/login', [AuthController::class, 'login']);
$app->get('/admin/register', [AuthController::class, 'registerForm']);
$app->post('/admin/register', [AuthController::class, 'register']);
$app->get('/verify-email', [AuthController::class, 'verifyEmail']);
$app->get('/admin/logout', [AuthController::class, 'logout']);

// Redirecionar /admin/ para /admin (remover barra final)
$app->get('/admin/', function ($request, $response) {
    return $response->withHeader('Location', '/admin')->withStatus(301);
});

// === ROTAS ADMIN (protegidas) ===
$app->group('/admin', function ($group) {
        // Clientes
        $group->get('/clientes', [AdminController::class, 'listCustomers']);
    $group->get('', [AdminController::class, 'dashboard']);

    // Uploads user admin
    $group->post('/upload-logo', [AdminController::class, 'uploadLogo']);

    // Produtos
    $group->get('/products', [AdminController::class, 'listProducts']);
    $group->get('/products/new', [AdminController::class, 'newProduct']);
    $group->post('/products', [AdminController::class, 'createProduct']);
    $group->get('/products/{id}/edit', [AdminController::class, 'editProduct']);
    $group->post('/products/{id}', [AdminController::class, 'updateProduct']);
    $group->post('/products/{id}/delete', [AdminController::class, 'deleteProduct']);
    
    // Categorias
    $group->get('/categories', [AdminController::class, 'listCategories']);
    $group->get('/categories/new', [AdminController::class, 'newCategory']);
    $group->post('/categories', [AdminController::class, 'createCategory']);
    $group->get('/categories/{id}/edit', [AdminController::class, 'editCategory']);
    $group->post('/categories/{id}', [AdminController::class, 'updateCategory']);
    $group->post('/categories/{id}/delete', [AdminController::class, 'deleteCategory']);
    
    // Ingredientes
    $group->get('/ingredients', [AdminController::class, 'listIngredients']);
    $group->get('/ingredients/new', [AdminController::class, 'newIngredient']);
    $group->post('/ingredients', [AdminController::class, 'createIngredient']);
    $group->get('/ingredients/{id}/edit', [AdminController::class, 'editIngredient']);
    $group->post('/ingredients/{id}', [AdminController::class, 'updateIngredient']);
    $group->post('/ingredients/{id}/delete', [AdminController::class, 'deleteIngredient']);
    
    // Pedidos (OrderController - versão completa)
    $group->get('/pedidos', [OrderController::class, 'listOrders']);
    $group->get('/pedidos/novo', [AdminController::class, 'newTableOrder']);
    $group->post('/pedidos/novo', [AdminController::class, 'createTableOrder']);
    $group->post('/pedidos/{id}/status', [AdminController::class, 'updateOrderStatus']);
    $group->get('/pedidos/{id}', [OrderController::class, 'viewOrder']);
    
    // Relatórios Financeiros
    $group->get('/relatorios', [ReportController::class, 'dashboard']);
    $group->get('/relatorios/diario', [ReportController::class, 'dailyReport']);
    $group->get('/relatorios/api/chart-data', [ReportController::class, 'getChartData']);
    
    // Configurações da Loja
    $group->get('/loja/configuracoes', [StoreController::class, 'settings']);
    $group->post('/loja/configuracoes', [StoreController::class, 'updateSettings']);
    $group->get('/loja/fidelidade/{customerId}', [StoreController::class, 'checkCustomerLoyalty']);
    
})->add(function ($request, $handler) {
    // Verificar se usuário está logado
    if (!isset($_SESSION['user_id'])) {
        $response = new \Slim\Psr7\Response();
        return $response->withHeader('Location', '/admin/login')->withStatus(302);
    }
    
    // Validar sessão no banco (verificar IP e User-Agent para detectar hijacking)
    $sessionService = $this->get(\App\Services\SessionService::class);
    $serverParams = $request->getServerParams();
    $sessionId = session_id();
    $ipAddress = $serverParams['REMOTE_ADDR'] ?? '0.0.0.0';
    $userAgent = $serverParams['HTTP_USER_AGENT'] ?? 'Unknown';
    
    if (!$sessionService->validateSession($sessionId, $ipAddress, $userAgent)) {
        // Sessão inválida ou hijacking detectado
        session_destroy();
        $_SESSION = [];
        $response = new \Slim\Psr7\Response();
        return $response->withHeader('Location', '/admin/login?error=session_invalid')->withStatus(302);
    }
    
    return $handler->handle($request);
});

// === ROTAS PÚBLICAS DA LOJA ===
$app->group('/{store}', function ($group) {
    $group->get('/meus-pedidos', [CartController::class, 'orders']);
    // Cardápio da loja
    $group->get('', [MenuController::class, 'index']);
    $group->get('/categoria/{category}', [MenuController::class, 'index']);
    
    // API para produtos
    $group->get('/api/product/{id}', [MenuController::class, 'getProduct']);
    $group->post('/api/calculate-price', [MenuController::class, 'calculatePrice']);
    
    // Carrinho
    $group->get('/carrinho', [CartController::class, 'index']);
    $group->post('/carrinho/adicionar', [CartController::class, 'addItem']);
    $group->get('/carrinho/remover/{cart_id}', [CartController::class, 'removeItem']);
    $group->post('/carrinho/sync', [CartController::class, 'syncCart']);
    $group->get('/api/last-order-by-phone', [OrderController::class, 'getLastOrderByPhone']);
    
    // Checkout
    $group->get('/checkout', [CartController::class, 'checkout']);
    $group->post('/checkout', [CartController::class, 'processOrder']);
});

// Rotas de acompanhamento de pedido (fora do grupo para não conflitar)
$app->get('/order/{store}', [OrderController::class, 'trackOrder']);
$app->get('/querodenovo/{id}', [OrderController::class, 'repeatOrder']);


//** === ROTA PRINTPEDIDO
$app->get('/imprimir-pedido/{id}', [PrintController::class, 'printOrder']);
$app->get('/admin/print-order/{id}', [PrintController::class, 'printDataJson']);
$app->get('/admin/print-order-pdf/{id}', [PrintController::class, 'printOrderPDF']);
$app->get('/api/last-order-id', [OrderController::class, 'getLastOrderId']); /**/
// === LOGS
//$app->get('/admin/logs', function ($request, $response) {
    //$log = file_get_contents(__DIR__ . '/../storage/logs/app.log');
    //$lines = explode("\n", $log);
    //$html = '<style>
        //body{background:#111;color:#eee;font-family:monospace;}
        //.json-log{background:#222;margin:10px 0;padding:10px;border-radius:6px;}
        //.level{font-weight:bold;}
        //.level-info{color:red;}
        //.level-error{color:#f00;}
       // .level-warning{color:#ff0;}
        //.date{color:green;}
       // .msg{color:#fff;}
   // </style>';
    //$html .= '<h3>Logs</h3>';
    //foreach ($lines as $line) {
    //    $line = trim($line);
     //   if ($line) {
     //       $data = json_decode($line, true);
      //      if ($data) {
      //         $level = strtolower($data['level_name'] ?? 'info');
       //         $html .= '<div class="json-log">';
         //       $html .= '<span class="date">' . htmlspecialchars($data['datetime'] ?? '') . '</span> ';
           //     $html .= '<span class="level level-' . $level . '">' . htmlspecialchars($data['level_name'] ?? '') . '</span> ';
             //   $html .= '<span class="msg">' . htmlspecialchars($data['message'] ?? '') . '</span><br>';
               // if (!empty($data['context'])) {
                 //   $html .= '<pre style="background:#333;color:#0ff;padding:5px;">' . htmlspecialchars(json_encode($data['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
            //    }
                //$html .= '</div>';
          //  } else {
               // $html .= '<pre class="json-log">' . htmlspecialchars($line) . '</pre>';
        //    }
       // }
   // }
   // $response->getBody()->write($html);
   // return $response;
//}); /**/

// Rota padrão - redireciona para admin
$app->get('/', function (Request $request, Response $response) {
    return $response->withHeader('Location', '/admin/login')->withStatus(302);
}); 

// Roda a aplicação
$app->run();
