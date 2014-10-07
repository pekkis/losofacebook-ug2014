<?php

require_once __DIR__.'/../vendor/autoload.php';

use Knp\Provider\ConsoleServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Application;
use Losofacebook\Service\ImageService;
use Losofacebook\Service\PersonService;
use Losofacebook\Service\PostService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Silex\Provider\SessionServiceProvider;
use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Provider\MonologServiceProvider;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\FirePHPHandler;

$app = new Silex\Application();
$app['debug'] = true;

// Simulated login
$app['dispatcher']->addListener(KernelEvents::REQUEST, function (KernelEvent $event) use ($app) {
    $app['session']->set('user', array('username' => 'gaylord.lohiposki'));

    // $logger = new Doctrine\DBAL\Logging\EchoSQLLogger();
    // $conn->getConfiguration()->setSQLLogger($logger);

});

// Providers

$app->register(new ConsoleServiceProvider(), array(
    'console.name'              => 'Losonaamakirja',
    'console.version'           => '1.0.0',
    'console.project_directory' => __DIR__.'/..'
));

$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_mysql',
        'host' => 'localhost',
        'port' => 3306,
        'dbname' => 'losofacebook',
        'user' => 'root',
        'password' => 'g04753m135',
        'charset' => 'utf8',
    ),
));

$app->register(
    new SessionServiceProvider(),
    [
        'session.storage.save_path' => __DIR__ . '/data/sessions',
        'session.storage.options' => [
            'name' => 'losofacebook',
        ]
    ]
);


$app['memcached'] = $app->share(function (Application $app) {
    $m = new Memcached();
    $m->addServer('localhost', 11211);
    // $m->setOption(Memcached::OPT_COMPRESSION, false);
    return $m;
});


$app['neo'] = $app->share(function (Application $app) {
    $client = new Everyman\Neo4j\Client('localhost', 7474);
    return $client;
});


$app['personService'] = $app->share(function (Application $app) {
    return new PersonService($app['neo'], $app['memcached']);
});

$app['imageService'] = $app->share(function (Application $app) {
    return new ImageService(
        $app['neo'],
        realpath(__DIR__ . '/data/images'),
        $app['memcached']
    );
});


$app['postService'] = $app->share(function (Application $app) {

    return new PostService(
        $app['neo'],
        $app['memcached']
    );

});

$app->get('/api/person/{username}', function(Application $app, $username) {

    /** @var PersonService $personService */
    $personService = $app['personService'];

    $person = $personService->findByUsername($username);

    return new JsonResponse(
        $person
    );

});

$app->get('/api/post/{personId}', function(Application $app, Request $request, $personId) {

    /** @var PostService $postService */
    $postService = $app['postService'];

    $posts = $postService->findByPersonId($personId);

    return new JsonResponse(
        $posts
    );

});

return $app;
