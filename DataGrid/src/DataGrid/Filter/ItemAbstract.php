<?php
namespace DataGrid\Filter;


use DataGrid\DataSource\DataSourceInterface;

abstract class ItemAbstract implements ItemInterface
{
    protected $value = null;
    protected $name = 'abstract';

    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    public function setOptions($options)
    {
        foreach($options as $k=>$v)
        {
            $call = 'set'.ucfirst($k);
            if(method_exists($this, $call))
            {
                $this->$call($v);
            }
        }
        return $this;
    }

    private $label = null;

    /**
     * @return null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param null $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getValue()
    {
        if(null == $this->value && !empty($_GET[$this->getName()])){
            $this->setValue($_GET[$this->getName()]);
        }
        return $this->value;
    }

    /**
     * @param null $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    abstract public function applyTo(DataSourceInterface $dataSource);
}