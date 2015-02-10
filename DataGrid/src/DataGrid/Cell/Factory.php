<?php
namespace DataGrid\Cell;

use DataGrid\Exception;

class Factory
{
    const TYPE = 'type';

    private static $cellTypes = array(
        'text' => '\DataGrid\Cell\Text',
        'boolean' => '\DataGrid\Cell\Boolean',
        'union' => '\DataGrid\Cell\Union',
        'action' => '\DataGrid\Cell\Action'
    );

    public function get($type, $config = null)
    {
        if(empty($config) && is_array($type)){
            $config = $type;
            $type = $config[self::TYPE];
        }
        $cellClass = $this->getCellClass($type);
        $cell = new $cellClass($config);
        return $cell;
    }

    public static function registerCellType($type, $class)
    {
        self::$cellTypes[strtolower($type)] = $class;
    }

    public function getCellClass($type)
    {
        if(empty(self::$cellTypes[strtolower($type)])){
            throw new Exception('Cell Type "' . $type . '" is not registered');
        }
        return self::$cellTypes[strtolower($type)];
    }
}