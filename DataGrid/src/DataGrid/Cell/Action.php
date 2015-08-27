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
            $vars += parent::getContentVariables($withModifiers, $param);
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
//        $data = $this->getData();
        $variables = $this->getContentVariables(true);
        $dataToReplace = array();
        foreach($variables as $mask => $var)
        {
            $var = $this->getData($var);
            if(gettype($var) == 'object')
            {
                throw new Exception\ObjectValueException('Grid can not render complex values. Try to apply some modifier to value "' . $mask . '"');
            }
            $dataToReplace[$mask] = $var;
        }

        $urlParams = $this->getContent();
        foreach ($urlParams as $k => $param) {
            $urlParams[$k] = str_replace(
                array_keys($dataToReplace),
                array_values($dataToReplace),
                $param
            );
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