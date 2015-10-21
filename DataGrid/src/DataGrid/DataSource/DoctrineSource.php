<?php
namespace DataGrid\DataSource;

use DataGrid\CellSet;
use DataGrid\Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;

class DoctrineSource implements DataSourceInterface, DataSourceFilterableInterface, DataSourcePaginatedInterface, DataSourceSortableInterface
{

    const QUERY_ALIAS_SEPARATOR = ':';

    private $source = null;
    private $cellssetDecorator = null;
    private $currentAlias = 't';

    private $queryResult = null;

    private $maxResults = null;
    private $page = null;

    private $iteratorPosition = 0;

    public function __construct()
    {
    }

    public function setSource($source)
    {
        if ($source instanceof EntityRepository) {
            $this->source = $source->createQueryBuilder($this->currentAlias);
        } elseif ($source instanceof QueryBuilder) {
            $this->source = $source;
            /**
             * @vr \Doctrine\ORM\Query\Expr\From $from
             */
            $from = $this->source->getDQLPart('from')[0];
            $this->setCurrentAlias($from->getAlias());
        } else {
            throw new Exception('Doctrine Adapter can handle only doctrine repository or Doctrine Query Builder');
        }
        return $this;
    }

    public function orFilterGroup($fields, $joiner='OR')
    {
        $conditions = array();
        foreach($fields as $field=>$data){
            $conditions[] = $this->compileFilterParams($field, $data['value'], $data['type']);
        }
        $this->source->orWhere('(' . implode(' ' . $joiner . ' ', $conditions) . ')');
    }

    public function andFilterGroup($fields, $joiner='OR')
    {
        $conditions = array();
        foreach($fields as $field=>$data){
            $conditions[] = $this->compileFilterParams($field, $data['value'], $data['type']);
        }
        $this->source->andWhere('(' . implode(' ' . $joiner . ' ', $conditions) . ')');
    }

    public function andFilter($field, $value, $type = self::FILTER_TYPE_EQ)
    {
        $this->source->andWhere($this->compileFilterParams($field, $value, $type));
        return $this;
    }

    public function orFilter($field, $value, $type = self::FILTER_TYPE_EQ)
    {
        $this->source->orWhere($this->compileFilterParams($field, $value, $type));
        return $this;
    }

    protected function compileFilterParams($field, $value, $type = self::FILTER_TYPE_EQ)
    {
        $dbField = !strpos($field, '.') ? $this->currentAlias . '.' . $field : $field;
        $paramName = str_replace('.', '_', $field) . '_' . uniqid();
        switch ($type) {
            case self::FILTER_TYPE_EQ:
                $condition = $dbField . ' = :' . $paramName;
                $this->source->setParameter($paramName, $value);
                break;
            case self::FILTER_YPE_CONTAINS:
                $condition = $dbField . ' LIKE :' . $paramName;
                $this->source->setParameter($paramName, '%' . $value . '%');
                break;
            default:
                throw new \RuntimeException('Filter comparison type "' . $type . '" is not supported by Doctrine grid source');
        }
        return $condition;
    }

    public function clearFilters()
    {
        $this->source->resetDQLPart('where');
        $this->source->setParameters(array());
        return $this;
    }

    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;
        return $this;
    }

    public function getMaxResults()
    {
        return $this->maxResults;
    }

    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function bindCellsset(CellSet $cellsset)
    {
        $this->cellssetDecorator = $cellsset;
        return $this;
    }

    /**
     * @return CellsSet
     */
    public function getCellssetDecorator()
    {
        return $this->cellssetDecorator;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->getQueryResult($this->iteratorPosition);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->iteratorPosition++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->iteratorPosition;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *       Returns true on success or false on failure.
     */
    public function valid()
    {
        return null != $this->getQueryResult($this->iteratorPosition);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->iteratorPosition = 0;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *       </p>
     *       <p>
     *       The return value is cast to an integer.
     */
    public function count()
    {
        $this->assembleQueryWithJoins();
        $q = clone $this->source;
        $q = $q->resetDQLPart('orderBy')->select('COUNT(' . $this->currentAlias . ')')->getQuery();
        return $q->getSingleScalarResult();
    }

    public function addOrderBy($field, $direction)
    {
        $this->source->addOrderBy($this->currentAlias . '.' . $field, $direction);
        return $this;
    }

    public function resetOrderBy()
    {
        $this->source->resetDQLPart('orderBy');
        return $this;
    }

    private function getQueryResult($key = null)
    {
        if (null == $this->queryResult) {
            if (null != $this->getCellssetDecorator()) {
                $this->assembleQueryWithJoins();
                /**
                 * @var \DataGrid\Cell\Cell $cell
                 */
                foreach ($this->getCellssetDecorator() as $cell) {
                    $cellDirection = $cell->getCurrentOrderDirection();
                    $orderResetted = false;
                    if (!empty($cellDirection)) {
                        if (!$orderResetted) {
                            $this->resetOrderBy();
                        }
                        $orderFields = explode(',', $cell->getOrderBy());
                        foreach ($orderFields as $field) {
                            $this->source->addOrderBy($this->currentAlias . '.' . $field, $cellDirection);
                        }
                    }
                }
            }
            $q = $this->source->getQuery();

            if (null !== $this->getMaxResults()) {
                $q->setMaxResults($this->getMaxResults());
                if (null != $this->getPage()) {
                    $q->setFirstResult($this->getOffset());
                }
            }
            $this->queryResult = $q->getResult((null != $this->getCellssetDecorator()) ? \Doctrine\ORM\Query::HYDRATE_ARRAY : \Doctrine\ORM\Query::HYDRATE_OBJECT);
        }
        if (null !== $key) {
            if (!isset($this->queryResult[$key])) {
                return null;
            }
            return $this->decorateWithCellsset($this->queryResult[$key]);
        }

        return $this->queryResult;
    }

    private function assembleQueryWithJoins(){
        $joins = $this->getJoins();
        /**
         * @var \Doctrine\ORM\Query\Expr\Select $select
         */
        $select = $this->source->getDQLPart('select')[0];
        foreach ($this->getJoinFields() as $field) {
            $fieldQueryAlias = str_replace('.', self::QUERY_ALIAS_SEPARATOR, $field);
            if (!array_key_exists($fieldQueryAlias, $joins)) {
                $this->source->leftJoin($this->currentAlias . '.' . $field, $fieldQueryAlias, Expr\Join::WITH);
                $select->add($fieldQueryAlias);
            }
        }
        return $this->source;
    }

    private function getJoins()
    {
        $aliases = array();
        /**
         * @var \Doctrine\ORM\Query\Expr\Join $join
         */
        $joins = $this->source->getDQLPart('join');
        if (!empty($joins)) {
            foreach ($joins[$this->currentAlias] as $join) {
                $aliases[$join->getAlias()] = $join;
            }
        }

        return $aliases;
    }

    private function getJoinFields()
    {
        $fields = array();
        if (null != $this->getCellssetDecorator()) {
            foreach ($this->getCellssetDecorator()->getVariables() as $var) {
                if (false !== strpos($var, '.')) {
                    $fields[] = substr($var, 0, strrpos($var, '.'));
                }
            }
        }
        return array_unique($fields);
    }

    private function decorateWithCellsset($entity)
    {
        if($this->getCellssetDecorator() != null){
            $entity = $this->convertEntityToPlainArray($entity);
            $decorator = clone $this->getCellssetDecorator();
            if (null == $decorator) {
                return $entity;
            }
            return $decorator->setData($entity);
        }
        return $entity;
    }

    private function convertEntityToPlainArray($data, $prefix = '')
    {
        $out = array();
        foreach ($data as $k => $v) {

            if (($k === 0 && count($data) == 1)) {
                $out += $this->convertEntityToPlainArray($v, $prefix);
            } elseif (is_array($v) && array_key_exists(0, $v)) {
                $out += $this->convertEntityToPlainArray($v[0], $prefix . $k . '.');
            } else {
                if (is_array($v)) {
                    $out += $this->convertEntityToPlainArray($v, $k . '.');
                } else {
                    $out[$prefix . $k] = $v;
                }

            }
        }
        return $out;
    }

    private function getOffset()
    {
        if (null == $this->getPage() || null == $this->getMaxResults()) {
            return 0;
        }
        return ($this->getPage() - 1) * $this->getMaxResults();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return null == $this->getQueryResult($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     * @return mixed
     * @throws Exception
     */
    public function offsetGet($offset)
    {
        $data = $this->getQueryResult($offset);
        if (null == $data) {
            throw new Exception('Data row by key "' . $offset . '" not exists');
        }
        return $data;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value <p>
     *                      The value to set.
     *                      </p>
     *
     * @throws Exception
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception('Core Grid Data Source is read only.');
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @throws Exception
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new Exception('Core Grid Data Source is read only.');
    }

    /**
     * @return string
     */
    public function getCurrentAlias()
    {
        return $this->currentAlias;
    }

    /**
     * @param string $currentAlias
     */
    public function setCurrentAlias($currentAlias)
    {
        $this->currentAlias = $currentAlias;
    }
}