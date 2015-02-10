<?php
namespace DataGrid\Cell;

use DataGrid\Exception;

class Boolean extends Cell
{
    private $valueTrue = true;

    private function convertType($val, $toType)
    {
        if (!settype($val, $toType)) {
            throw new Exception('Can not convert value "' . $val . '" to type "' . $toType . '"');
        }
        return $val;
    }

    /**
     * @return boolean
     */
    public function getValueTrue()
    {
        return $this->valueTrue;
    }

    /**
     * @param boolean $valueTrue
     * @return $this
     */
    public function setValueTrue($valueTrue)
    {
        $this->valueTrue = $valueTrue;
        return $this;
    }

    protected function renderContent()
    {
        $content = $this->getContent();
        if(empty($content)){
            return false;
        }
        $value = $this->getData($content);
        $valueToCompare = $this->getValueTrue();

        $valueType = gettype($value);
        $compareType = gettype($valueToCompare);
        if ($valueType !== $compareType) {
            $value = $this->convertType($value, $compareType);
        }
        return $value === $valueToCompare;
    }

    public function getContentVariables($withModifiers = false)
    {
        return array($this->getContent());
    }
}