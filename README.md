PSR7BridgeServiceProvider
=========================

Installation
------------

Add the following code to composer.json.

```javascript
{
    "repositories": [
    {
        "type": "package",
        "package": {
            "name": "masakielastic/silex-psr7bridge-service-provider",
            "version": "0.1.0",
            "type": "package",
            "source": {
                "url": "https://github.com/masakielastic/silex-psr7bridge-service-provider.git",
                "type": "git",
                "reference": "master"
            },
            "autoload": {
                "psr-4": { "Masakielastic\\Silex\\": "src/" }
            }
        }
    }
    ],
    "require": {
        "silex/silex": "~1.3",
        "symfony/psr-http-message-bridge": "0.2.*",
        "zendframework/zend-diactoros": "~1.1",
        "psr/http-message": "~1.0",
        "masakielastic/silex-psr7bridge-service-provider": "*"
    }
}

```

Usage
-----

```php
use Silex\Application;
use Masakielastic\Silex\Psr7BridgeServiceProvider;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response as Psr7Response;

$app = new Application();
$app->register(new Psr7BridgeServiceProvider);

$app->get('/', function(ServerRequestInterface $request) {
    $method = $request->getMethod();
    $response = new Psr7Response();
    $response->getBody()->write($method);

    return $response;
});

$app->run();
```

License
-------

MIT

