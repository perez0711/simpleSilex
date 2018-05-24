<?php

$loader = require __DIR__.'/vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

use Silex\Provider\TwigServiceProvider;
use Silex\Application as SilexApplication;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use DerAlex\Silex\YamlConfigServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use fiunchinho\Silex\Provider\RabbitServiceProvider;
use SimpleApi\Mongo\Silex\Provider\MongoServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;

if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

class Application extends SilexApplication
{
    use Silex\Application\TranslationTrait;
    use Silex\Application\TwigTrait;
    use Silex\Application\UrlGeneratorTrait;
}

$app = new Application();

define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
$app['debug'] = false;
$app->register(new YamlConfigServiceProvider(__DIR__.'/config/config.yml'));

$configMonolog      = $app['config']['monolog'];
$configTwig         = $app['config']['twig'];
$configDoctrine     = $app['config']['doctrine']['options'];
$configDoctrineOrm  = $app['config']['doctrine']['orm'];
$configParams       = $app['config']['params'];
$configTranslator   = $app['config']['translate'];
$confTransCfStdLang = $app['config']['translator_config']['default_lang'];
$configRabbit       = $app['config']['rabbit'];
$configMongo        = $app['config']['mongo'];

$configMonolog['monolog.logfile'] = "/var/www/html/simpleApiSilex/data/log/simple_api.log";

$configDoctrineOrm['orm.proxies_dir'] = ROOT_PATH . 'data/DoctrineORM/Proxy';

$configParams['key']['value'] = "BOM DIA";

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new MonologServiceProvider(), $configMonolog);
$app->register(new TwigServiceProvider(), $configTwig);
$app->register(new DoctrineServiceProvider, $configDoctrine);
$app->register(new DoctrineOrmServiceProvider(), $configDoctrineOrm);
$app->register(new TranslationServiceProvider(), $configTranslator);
$app->register(new RabbitServiceProvider(), $configRabbit);
$app->register(new MongoServiceProvider(), $configMongo);


$app['translator'] = $app->share($app->extend('translator', function ($translator, $app) {
    $translator->addLoader('yaml', new Symfony\Component\Translation\Loader\YamlFileLoader());
    
    foreach (glob(__DIR__ . '/resources/locale/*.yml') as $locale) {
        $lang = str_replace(".yml", "", basename($locale));
        $translator->addResource('yaml', $locale, $lang);
    }
    return $translator;
}));

$app['translator']->setLocale($confTransCfStdLang);

$app['simple_api.params'] = $configParams;

define("GLOBAL", $app['simple_api.params']['key']['value']);
define("ELASTICSEARCH", "localhost");

$apiCtrl = new SimpleApi\Api();
$app->mount('/api/v1', $apiCtrl);

$authCtrl = new SimpleApi\Auth();
$app->mount('/auth/v1', $authCtrl);

return $app;