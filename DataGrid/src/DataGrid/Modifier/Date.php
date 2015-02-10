<?php
namespace DataGrid\Modifier;

class Date extends Base
{
    /**
     * @param Zend_Date $value
     *
     * @return mixed
     */
    public function modify($value)
    {
        if(!empty($value)) {
            $options = $this->getOptions();
            if(!($value instanceof \DateTime))
            {
                $value = new \DateTime($value);
            }
            return $value->format($options[0]);
        }

        return null;
    }
}