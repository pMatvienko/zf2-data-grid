<?php
namespace DataGrid\Condition;

interface PublicInterface{
    /**
     * Calculating condition by given values.
     *
     * @param array $values
     *
     * @return boolean
     */
    public function calculate($values);

    /**
     * gets variables used in condition.
     *
     * @return array()
     */
    public function getVariables();
}