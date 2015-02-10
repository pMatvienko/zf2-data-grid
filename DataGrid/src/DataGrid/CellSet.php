<?php
namespace DataGrid;

class CellSet implements \Countable, \Iterator, \ArrayAccess
{
    private $cells = array();
    private $id='';

    public function __construct(array $configuration = array())
    {
        if(is_array($configuration)) {
            foreach($configuration as $cellAlias => $cell)
            {
                $this->appendCell($cell, $cellAlias);
            }
        }
    }

    /**
     * @param $cell
     * @param null $name
     * @return $this
     */
    public function prependCell($cell, $name=null)
    {
        $cell = $this->buildCell($cell);
        if($name == null && $cell->getName() != '') {
            $name = $cell->getName();
        }
        if($name == null){
            array_unshift($this->cells, $cell);
        } else {
            $this->cells = array($name => $cell)+$this->cells;
        }
        reset($this->cells);
        $cell->setName(key($this->cells));
        return $this;
    }

    /**
     * @param $cell
     * @param null $name
     * @return $this
     */
    public function appendCell($cell, $name=null)
    {
        $cell = $this->buildCell($cell);
        if($name == null && $cell->getName() != '') {
            $name = $cell->getName();
        }

        if($name == null){
            $this->cells[] = $cell;
        } else {
            $this->cells[$name] = $cell;
        }
        end($this->cells);
        $cell->setName(key($this->cells));

        return $this;
    }

    public function getCell($offset)
    {
        return $this->cells[$offset]->setId($this->getId());
    }

    private function buildCell($cell)
    {
        if(is_array($cell)){
            $factory = new Cell\Factory();
            $cell = $factory->get($cell);
        }
        return $cell;
    }

    public function removeCell($name)
    {
        if(!array_key_exists($name, $this->cells)){
            throw new Exception('Cell "' . $name . '" is not set, so could not be removed');
        }
        unset($this->cells[$name]);
        return $this;
    }

    /**
     * @return Cell\Cell[]
     */
    public function getCells()
    {
        foreach($this->cells as $cell){
            $cell->setId($this->getId());
        }
        return $this->cells;
    }

    public function setData(&$data)
    {
        /**
         * @var Cell\Cell $cell
         */
        foreach ($this->cells as $cell) {
            $cell->setData($data);
        }
        return $this;
    }

    public function getVariables()
    {
        $vars = array();
        /**
         * @var Cell\Cell $cell
         */
        foreach ($this->getCells() as $cell) {
            $vars += $cell->getContentVariables();
        }
        return $vars;
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
        return current($this->cells);
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
        next($this->cells);
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
        return key($this->cells);
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
        return isset($this->cells[$this->key()]);
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
        reset($this->cells);
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
        return isset($this->cells[$offset]);
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
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->getCell($offset);
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
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
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
     * @return void
     */
    public function offsetUnset($offset)
    {
        return $this->removeCell($offset);
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
        return count($this->cells);
    }

    public function getColumnsToHideIfUnavailable()
    {
        $out = array();
        foreach($this->getCells() as $key => $cell)
        {
            if($cell->getHideCellIfUnavailable())
            {
                $out[] = $key;
            }
        }
        return $out;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}