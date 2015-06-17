<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 8/06/15
 * Time: 17:47
 */

namespace Scastells\ConektaBundle\Router;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class SpeiRouterLoader implements LoaderInterface
{

    /**
     * @var string
     * Execution controller route name
     */
    protected $controllerSpeiRouteName;

    /**
     * @var string
     * Execution controller route
     */
    protected $controllerSpeiRoute;

    /**
     * @var string
     * Execution controller route notification
     */
    protected $controllerSpeiNotifyRoute;

    /**
     * @var string
     * Controller notify name
     */
    protected $controllerSpeiNotifyRouteName;

    protected $loaded = false;

    /**
     * Construct method
     *
     * @param string $controllerSpeiRouteName Controller route name
     * @param string $controllerSpeiRoute
     * @param        $controllerSpeiNotifyRoute
     * @param        $controllerSpeiNotifyRouteName
     *
     */
    public function __construct(
        $controllerSpeiRouteName,
        $controllerSpeiRoute,
        $controllerSpeiNotifyRoute,
        $controllerSpeiNotifyRouteName
    ) {
        $this->controllerSpeiRouteName = $controllerSpeiRouteName;
        $this->controllerSpeiRoute = $controllerSpeiRoute;
        $this->controllerSpeiNotifyRoute = $controllerSpeiNotifyRoute;
        $this->controllerSpeiNotifyRouteName = $controllerSpeiNotifyRouteName;
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
            $this->controllerSpeiRouteName,
            new Route($this->controllerSpeiRoute, array(
                '_controller' => 'ScastellsConektaBundle:Conekta:executeSpei',
            ))
        );
        $routes->add(
            $this->controllerSpeiNotifyRouteName,
            new Route($this->controllerSpeiNotifyRoute, array(
                '_controller' => 'ScastellsConektaBundle:Conekta:NotifySpei',
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
        return 'conekta_spei' === $type;
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