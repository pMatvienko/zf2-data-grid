<?php
namespace DataGrid;

class Paginator
{
    private $page=1;
    private $pageSize = 20;
    private $totalRecordsCount = 0;
    private $visiblePagesRange = 5;
    private $id = '';

    /**
     * @param $options
     */
    public function __construct($options = array())
    {
        foreach($options as $k=>$v)
        {
            $k = 'set'.ucfirst($k);
            if(method_exists($this, $k))
            {
                $this->$k($v);
            }
        }
    }

    /**
     * @return int
     */
    public function getPage()
    {
        if(!empty($_GET[$this->getId().'page'])){
            $this->page = $_GET[$this->getId().'page'];
        }
        return $this->page;
    }

    /**
     * @param int $page
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalRecordsCount()
    {
        return $this->totalRecordsCount;
    }

    /**
     * @param int $totalRecordsCount
     * @return $this
     */
    public function setTotalRecordsCount($totalRecordsCount)
    {
        $this->totalRecordsCount = $totalRecordsCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getVisiblePagesRange()
    {
        return $this->visiblePagesRange;
    }

    /**
     * @param int $visiblePagesRange
     * @return $this
     */
    public function setVisiblePagesRange($visiblePagesRange)
    {
        $this->visiblePagesRange = $visiblePagesRange;
        return $this;
    }

    public function getPagesCount()
    {
        return ceil($this->getTotalRecordsCount() / $this->getPageSize());
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