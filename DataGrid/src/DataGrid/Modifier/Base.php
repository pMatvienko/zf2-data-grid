<?php
namespace DataGrid\Modifier;

abstract class Base
{
    const PARAM_SPLITTER = '%';

    private $options = array();

    public function __construct($options = array())
    {
        $this->options = $options;
    }

    /**
     * @param $modifyString
     *
     * @return \DataGrid\Modifier\Base
     */
    public static function factory($modifyString)
    {
        $modifyString = explode(self::PARAM_SPLITTER, $modifyString);
        $modifier = '\\' . __NAMESPACE__ . '\\' . ucfirst($modifyString[0]);
        unset($modifyString[0]);
        return new $modifier(array_values($modifyString));
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    abstract public function modify($value);
}