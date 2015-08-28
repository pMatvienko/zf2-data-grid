<?php
namespace DataGrid\DataSource;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class Factory implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function get($source)
    {
        if ($source instanceof \Doctrine\ORM\QueryBuilder || $source instanceof \Doctrine\ORM\EntityRepository) {
            $adapterClass = 'Doctrine';
        } else {
            $type = gettype($source);
            if ($type == 'object') {
                $type = str_replace('\\', '_', get_class($source));
            }
            $adapterClass = usfirst($type);
        }

        return $this->getServiceLocator()->get('DataGrid\\DataSource\\' . $adapterClass)->setSource($source);
    }

}
