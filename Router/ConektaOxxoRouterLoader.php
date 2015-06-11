<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 8/06/15
 * Time: 17:47
 */

namespace Fancy\ConektaBundle\Router;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ConektaOxxoRouterLoader implements LoaderInterface
{

    /**
     * @var string
     * Execution controller route name
     */
    protected $controllerOxxoRouteName;

    /**
     * @var string
     * Execution controller route
     */
    protected $controllerOxxoRoute;

    /**
     * @var string
     * Execution controller route notification
     */
    protected $controllerOxxoNotifyRoute;

    protected $controllerOxxoNotifyRouteName;

    protected $loaded = false;

    /**
     * Construct method
     *
     * @param string $controllerOxxoRouteName Controller route name
     * @param string $controllerOxxoRoute
     * @param        $controllerOxxoNotifyRoute
     * @param        $controllerOxxoNotifyRouteName
     *
     */
    public function __construct(
        $controllerOxxoRouteName,
        $controllerOxxoRoute,
        $controllerOxxoNotifyRoute,
        $controllerOxxoNotifyRouteName
    ) {
        $this->controllerOxxoRouteName = $controllerOxxoRouteName;
        $this->controllerOxxoRoute = $controllerOxxoRoute;
        $this->controllerOxxoNotifyRoute = $controllerOxxoNotifyRoute;
        $this->controllerOxxoNotifyRouteName = $controllerOxxoNotifyRouteName;
    }
    /**
     * @inheritdoc
     */
    public function load($resource, $type = null)
    {
        if ($this->loaded) {
            throw new \RuntimeException('Do not add this loader twice');
        }
        $routes = new RouteCollection();
        $routes->add(
            $this->controllerOxxoRouteName,
            new Route($this->controllerOxxoRoute, array(
                '_controller' => 'FancyConektaBundle:Conekta:executeOxxo',
            ))
        );
        $routes->add(
            $this->controllerOxxoNotifyRouteName,
            new Route($this->controllerOxxoNotifyRoute, array(
                '_controller' => 'FancyConektaBundle:Conekta:NotifyOxxo',
            ))
        );
        $this->loaded = true;
        return $routes;
    }
    /**
     * @inheritdoc
     */
    public function supports($resource, $type = null)
    {
        return 'conekta_oxxo' === $type;
    }
    /**
     * @inheritdoc
     */
    public function getResolver()
    {
    }
    /**
     * @inheritdoc
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}