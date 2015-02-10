<?php
namespace DataGrid\Condition;

class Group implements PublicInterface
{
    const JOINER_AND = 'AND';
    const JOINER_OR = '';

    private $items = array();

    public function __construct($condition)
    {
        $this->items[] = $condition;
    }

    /**
     * Calculating condition by given values.
     *
     * @param array $values
     *
     * @return boolean
     */
    public function calculate($values)
    {
        // TODO: Implement calculate() method.
    }
}