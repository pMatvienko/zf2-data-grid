<?php
namespace DataGrid\Cell;

class Text extends Cell
{
    protected function renderContent()
    {
        $variables = $this->getContentVariables(true);
        $dataToReplace = array();
        foreach($variables as $mask => $var)
        {
            $var = $this->getData($var);
            if(gettype($var) == 'object')
            {
                throw new Exception\ObjectValueException('Grid can not render complex values. Try to apply some modifier to value "' . $mask . '"');
            }
            $dataToReplace[$mask] = $var;
        }

        return str_replace(
            array_keys($dataToReplace),
            array_values($dataToReplace),
            $this->getContent()
        );
    }
}