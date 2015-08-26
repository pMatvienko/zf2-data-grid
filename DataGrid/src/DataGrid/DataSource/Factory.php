<?php
namespace DataGrid\DataSource;

class Factory
{
    private $dataSourceAdapters = array(
        'array' => '\DataGrid\DataSource\ArraySource',
        'Doctrine\ORM\EntityRepository' => '\DataGrid\DataSource\DoctrineSource',
        'Doctrine\ORM\QueryBuilder' => '\DataGrid\DataSource\DoctrineSource',
    );

    public function get($source)
    {
        if($source instanceof \Doctrine\ORM\QueryBuilder || $source instanceof \Doctrine\ORM\EntityRepository){
            $adapterClass = '\DataGrid\DataSource\DoctrineSource';
        }
        else{
            $type = gettype($source);
            if($type == 'object'){
                $type = get_class($source);
            }
            $adapterClass = $this->getDataAdapterName($type);
        }
        
        $adapter = new $adapterClass($source);
        return $adapter;
    }

    /**
     * @param string $type
     * @return string
     * @throws \DataGrid\Exception
     */
    public function getDataAdapterName($type)
    {
        if(empty($this->dataSourceAdapters[$type])) {
            throw new \DataGrid\Exception('Datasource "' . $type . '" is not supported by any registered adapter');
        }
        return $this->dataSourceAdapters[$type];
    }

    /**
     * @param string $sourceType
     * @param string $adapterClass
     * @return $this
     */
    public function registerDataAdapter($sourceType, $adapterClass)
    {
        $this->dataSourceAdapters[$sourceType] = $adapterClass;
        return $this;
    }
}
