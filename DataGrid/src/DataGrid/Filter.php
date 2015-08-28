<?php
namespace DataGrid;


use DataGrid\Filter\Item\Factory;
use DataGrid\Filter\ItemInterface;

class Filter
{
    private $items = array();
    private $searchBox = null;
    private $attributes = array();

    /**
     * @return array
     */
    public function getItems()
    {
        $searchbox = $this->getSearchBox();
        return (null == $searchbox) ? $this->items : array('search' => $searchbox)+$this->items;
    }

    /**
     * @param $defination
     * @param null $name
     * @return $this
     */
    public function addItem($defination, $name = null)
    {
        if ($defination instanceof ItemInterface) {
            $item = $defination;
        } elseif (is_array($defination)) {
            $factory = new Factory();
            $item = $factory->get($defination);
        }

        if($item instanceof Filter\Item\Search)
        {
            if(null != $this->searchBox){
                throw new \RuntimeException('One filter can contain only one global search box');
            }
            $this->searchBox = $item;
            return $this;
        }

        if(null != $name){
            $this->items[$name] = $item;
        } else{
            $this->items[] = $item;
        }
        return $this;
    }

    /**
     * @return null
     */
    public function getSearchBox()
    {
        return $this->searchBox;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function addAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function compileAttributesString()
    {
        $attrs = array();
        foreach($this->attributes as $name => $val){
            $attrs[] = $name . '="' . $val . '"';
        }
        return implode(' ', $attrs);
    }

    public function applyTo(DataSource\DataSourceInterface $dataSource)
    {
        if(!($dataSource instanceof DataSource\DataSourceFilterableInterface)){
            throw new \RuntimeException('Provided grid data source is not filterable. You should remove fitlers section config for grid with provided data source');
        }
        $dataSource->clearFilters();
        foreach($this->getItems() as $item){
            $item->applyTo($dataSource);
        }
        return $dataSource;
    }
}