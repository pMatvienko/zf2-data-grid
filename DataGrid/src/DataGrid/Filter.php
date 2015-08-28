<?php
namespace DataGrid;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use DataGrid\Filter\ItemInterface;

class Filter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    /**
     * Filter Items collection.
     * @var \DataGrid\Filter\ItemInterface[]
     */
    private $items = array();

    /**
     * Search box item. Allowed only one search box for grid.
     * @var null|\DataGrid\Filter\ItemInterface
     */
    private $searchBox = null;
    private $attributes = array();

    /**
     * @var string
     */
    private $id = '';

    public function __construct(array $configuration = [])
    {
        foreach ($configuration as $k => $v) {
            $call = 'set' . ucfirst($k);
            if (method_exists($this, $call)) {
                $this->$call($v);
            }
        }
    }

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
            $item = $this->getServiceLocator()->get('DataGrid\Factory\FilterItem')->get($defination);
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

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}