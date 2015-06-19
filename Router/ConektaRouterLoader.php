<?php
/**
 * Created by PhpStorm.
 * User: scastells
 * Date: 18/06/15
 * Time: 18:55
 */

namespace Scastells\ConektaBundle\Router;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ConektaRouterLoader implements LoaderInterface
{

    const ROUTE_NAME = 'conekta_execute';

    /**
     * @var string
     * Controller Conekta
     */
    protected $controllerConekta;

    /**
     * @var bool
     *
     * Route is loaded
     */
    protected $loaded = false;

    /**
     * Construct method
     *
     * @param        $controllerConekta
     *
     */
    public function __construct($controllerConekta)
    {
        $this->controllerConekta = $controllerConekta;
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
            self::ROUTE_NAME, new Route($this->controllerConekta, array(
                '_controller' => 'ScastellsConektaBundle:Conekta:execute',
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
        return 'conekta' === $type;
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