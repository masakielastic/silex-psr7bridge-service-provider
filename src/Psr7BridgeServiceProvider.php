<?php

namespace Masakielastic\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\MessageInterface;

class Psr7BridgeServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['psr7_bridge.diactoros'] = $app->share(function () {
            return new DiactorosFactory();
        });

        $app['psr7_bridge.httpfoundation'] = $app->share(function () {
            return new HttpFoundationFactory();
        });

        $app->on('kernel.controller', function (FilterControllerEvent $event) use ($app) {
            $controller = $event->getController();
            $request = $event->getRequest();

            $supportedTypes = [
                'Psr\Http\Message\ServerRequestInterface',
                'Psr\Http\Message\RequestInterface',
                'Psr\Http\Message\MessageInterface',
            ];

            if (is_array($controller)) {
                $r = new \ReflectionMethod($controller[0], $controller[1]);
            } elseif (is_object($controller) && !$controller instanceof \Closure) {
                $r = new \ReflectionObject($controller);
                $r = $r->getMethod('__invoke');
            } else {
                $r = new \ReflectionFunction($controller);
            }

            foreach ($r->getParameters() as $param) {

                if (in_array($param->getClass()->getName(), $supportedTypes)) {

                    $request->attributes->set(
                        $param->name, 
                        $app['psr7_bridge.diactoros']->createRequest($request)
                    );

                    return;
                }

            }


        });

        $app->view(function (ResponseInterface $controllerResult) use ($app) {
            return $app['psr7_bridge.httpfoundation']->createResponse($controllerResult);
        });

        $app->view(function (MessageInterface $controllerResult) use ($app) {
            return $app['psr7_bridge.httpfoundation']->createResponse($controllerResult);
        });
    }

    public function boot(Application $app)
    {
    }
}