<?php
namespace DataGrid\Cell;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class Factory implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const TYPE = 'type';

    public function get($type, $config = null)
    {
        if(empty($config) && is_array($type)){
            $config = $type;
            $type = $config[self::TYPE];
        }
        $cell = $this->getServiceLocator()->get('DataGrid\\Cell\\' . ucfirst($type));
        $cell->setOptions($config);
        return $cell;
    }
}