<?php
namespace DataGrid\Filter\Item;

use DataGrid\Exception;

class Factory
{
    const TYPE = 'type';

    private static $itemTypes = array(
        'search' => '\DataGrid\Filter\Item\Search',
    );

    public function get($defination = null)
    {
        $item = $this->getItemClass($defination[self::TYPE]);
        $item = new $item($defination);

        return $item;
    }

    public static function registerItemType($type, $class)
    {
        self::$itemTypes[strtolower($type)] = $class;
    }

    public function getItemClass($type)
    {
        if(empty(self::$itemTypes[strtolower($type)])){
            throw new Exception('Filter item Type "' . $type . '" is not registered');
        }
        return self::$itemTypes[strtolower($type)];
    }
}