<?php
namespace DataGrid\Filter\Item;

use DataGrid\DataSource\DataSourceInterface;
use DataGrid\Filter\ItemAbstract;

class Search extends ItemAbstract
{
    const CONFIG_FIELDS = 'fields';

    protected $fields = array();
    protected $name = 'search';


    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function applyTo(DataSourceInterface $dataSource)
    {
        if($this->getValue() == null){
            return $dataSource;
        }
        $filterFields = array();
        foreach($this->fields as $field => $type){
            $filterFields[$field] = array(
                'type' => $type,
                'value' => $this->getValue()
            );
        }
        $dataSource->andFilterGroup($filterFields, 'OR');
//        exit();
        return $dataSource;
    }

}