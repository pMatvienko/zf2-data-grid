<?php
namespace DataGrid\Filter\Item;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class Factory implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const TYPE = 'type';

    public function get($defination = null)
    {
        $item = $this->getServiceLocator()->get('DataGrid\\Filter\\' . ucfirst($defination[self::TYPE]));
        $item->setOptions($defination);
        return $item;
    }
}