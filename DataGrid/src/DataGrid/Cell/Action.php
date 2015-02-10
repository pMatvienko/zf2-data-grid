<?php
namespace DataGrid\Cell;

use Zend\Mvc\ModuleRouteListener;

class Action extends Cell
{
    private static $serviceLocator = null;

    private $route = null;

    private $textLabel = '';

    /**
     * @param null $serviceLocator
     */
    public static function setServiceLocator($serviceLocator)
    {
        self::$serviceLocator = $serviceLocator;
    }

    /**
     * @return string
     */
    public function getTextLabel()
    {
        return $this->textLabel;
    }

    /**
     * @param string $textLabel
     * @return $this
     */
    public function setTextLabel($textLabel)
    {
        $this->textLabel = $textLabel;
        return $this;
    }

    /**
     * @return null
     */
    public function getServiceLocator()
    {
        return self::$serviceLocator;
    }

    public function getContentVariables($withModifiers = false)
    {
        $vars = array();
        foreach ($this->getContent() as $param) {
            if (false === strpos($param, '{$')) {
                continue;
            }
            $vars[$param] = trim($param, '{$}');
            if (!$withModifiers) {
                $vars[$param] = explode(self::MODIFIER_SPLITTER, $vars[$param]);
                $vars[$param] = $vars[$param][0];
            }
        }
        return $vars;
    }

    /**
     * @return null
     * @throws Exception
     */
    public function getRoute()
    {
        if ($this->route === null) {
            $this->route = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getMatchedRouteName();

            if ($this->route === null) {
                throw new Exception('RouteMatch does not contain a matched route name');
            }
        }
        return $this->route;
    }

    /**
     * @param null $route
     * @return $this
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    protected function renderContent()
    {
        $urlParams = $this->getContent();
        $data = $this->getData();
        foreach ($urlParams as $k => $param) {
            $param = trim($param, '{$}');
            if (array_key_exists($param, $data)) {
                $urlParams[$k] = $data[$param];
            }
        }

        $routeMatch = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch();
        if ($routeMatch !== null) {
            $routeMatchParams = $routeMatch->getParams();

            if (isset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER])) {
                $routeMatchParams['controller'] = $routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER];
                unset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER]);
            }

            if (isset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE])) {
                unset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE]);
            }

            $urlParams = array_merge($routeMatchParams, $urlParams);
        }

        /**
         * @var \Zend\Mvc\Router\Http\TreeRouteStack $router
         */
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        return $router->assemble($urlParams, array('name' => $this->getRoute()));
    }

}