<?php
namespace DataGrid\Condition;

class Pair implements PublicInterface
{
    const OPERATOR_EQ = '=';
    const OPERATOR_NEQ = '!=';
    CONST OPERATOR_GRATER = '>';
    const OPERATOR_LESS = '<';
    const OPERATOR_GRATER_EQ = '>=';
    const OPERATOR_LESS_EQ = '<=';
    const DEFAULT_DATETIME_MASK = 'dd MMMM, YYYY; HH:mm';

    private $availableOperators
        = array(
            self::OPERATOR_EQ,
            self::OPERATOR_NEQ,
            self::OPERATOR_GRATER,
            self::OPERATOR_LESS,
            self::OPERATOR_GRATER_EQ,
            self::OPERATOR_LESS_EQ
        );

    private $variables = array();
    private $operator = null;
    private $condition = null;

    /**
     * @param $condition
     * @throws Exception\OperatorNotFoundException
     */
    public function __construct($condition)
    {
        $this->condition = $condition;

        preg_match_all('/\{\$([^}]+)\}/', $this->condition, $variables);
        foreach ($variables[0] as $key => $mask) {
            $this->variables[$mask] = $variables[1][$key];
        }
        if (!preg_match('%' . implode('|', $this->availableOperators) . '%', $this->condition, $matches)) {
            throw new Exception\OperatorNotFoundException('Operator not found in condition "' . $this->condition . '"');
        }
        $this->operator = $matches[0];
    }

    /**
     * Calculating condition by given values.
     *
     * @param array $values
     *
     * @throws Exception\UnknownPairOperatorException
     * @return boolean
     */
    public function calculate($values)
    {
        $dataToReplace = array();
        foreach ($this->getVariables() as $mask => $var) {
            $var = $values[$var];
            if ($var instanceof \DateTime) {
                $var = new \Zend_Date($var->getTimestamp());
                $var = $var->get(self::DEFAULT_DATETIME_MASK);
            }
            $dataToReplace[$mask] = $var;
        }
        $condition = str_replace(array_keys($dataToReplace), array_values($dataToReplace), $this->condition);
        $condition = explode($this->operator, $condition);
        if (is_numeric($condition[0])) {
            $condition[0] = floatval($condition[0]);
        } else {
            $condition[0] = trim($condition[0]);
        }
        if (is_numeric($condition[1])) {
            $condition[1] = floatval($condition[1]);
        } else {
            $condition[1] = trim($condition[1]);
        }
        $out = null;
        switch ($this->operator) {
            case self::OPERATOR_EQ:
                $out = $condition[0] == $condition[1];
                break;
            case self::OPERATOR_NEQ:
                $out = $condition[0] != $condition[1];
                break;
            case self::OPERATOR_GRATER:
                $out = $condition[0] > $condition[1];
                break;
            case self::OPERATOR_GRATER_EQ:
                $out = $condition[0] >= $condition[1];
                break;
            case self::OPERATOR_LESS:
                $out = $condition[0] < $condition[1];
                break;
            case self::OPERATOR_LESS_EQ:
                $out = $condition[0] <= $condition[1];
                break;
            default:
                throw new Exception\UnknownPairOperatorException('Operator "' . $this->operator . '" is not supported.');
        }
        return $out;
    }

    /**
     * gets variables used in condition.
     *
     * @return array()
     */
    public function getVariables()
    {
        return $this->variables;
    }
}