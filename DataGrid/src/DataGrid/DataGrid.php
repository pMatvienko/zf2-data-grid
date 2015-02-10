<?php
namespace DataGrid;

use DataGrid\DataSource\DataSourceInterface;

class DataGrid
{
    /**
     * @var DataSourceInterface
     */
    private $dataSource;
    private $cells;
    private $paginator = null;
    private $id = '';
    private $defaultOrderField = null;
    private $defaultOrderDirection = null;


    public function appendCell($definetion, $name = null)
    {
        $this->getCells()->appendCell($definetion, $name);
        return $this;
    }

    public function prependCell($definetion, $name = null)
    {
        $this->getCells()->prependCell($definetion, $name);
        return $this;
    }

    /**
     * @return CellSet
     */
    public function getCells()
    {
        if ($this->cells == null) {
            $this->cells = new CellSet();
        }
        $this->cells->setId($this->getId());
        return $this->cells;
    }

    /**
     * @return DataSourceInterface
     */
    public function getDataSource()
    {
        if (null != $this->getDefaultOrderDirection() && null != $this->getDefaultOrderField()) {
            $this->dataSource->addOrderBy($this->getDefaultOrderField(), $this->getDefaultOrderDirection());
        }
        if (null != $this->getPaginator()) {
            $this->dataSource->applyPaginator($this->getPaginator());
        }
        return $this->dataSource->bindCellsset($this->getCells());
    }

    /**
     * @param mixed $dataSource
     * @return $this
     */
    public function setDataSource($dataSource)
    {
        if (!$dataSource instanceof DataSourceInterface) {
            $factory = new \DataGrid\DataSource\Factory();
            $dataSource = $factory->get($dataSource);
        }
        $this->dataSource = $dataSource;
        return $this;
    }

    /**
     * @return \DataGrid\Paginator
     */
    public function getPaginator()
    {
        if(!empty($this->paginator)){
            $this->paginator->setId($this->getId());
        }
        return $this->paginator;
    }

    /**
     * @param null $paginator
     * @return $this
     */
    public function setPaginator($paginator)
    {
        if (is_array($paginator)) {
            $paginator = new Paginator($paginator);
        }
        $this->paginator = $paginator;
        return $this;
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

    /**
     * @return null
     */
    public function getDefaultOrderField()
    {
        return $this->defaultOrderField;
    }

    /**
     * @return null
     */
    public function getDefaultOrderDirection()
    {
        return $this->defaultOrderDirection;
    }

    /**
     * @param null $defaultOrderField
     * @return $this
     */
    public function setDefaultOrder($defaultOrderField, $defaultOrderDirection = 'ASC')
    {
        $this->defaultOrderField = $defaultOrderField;
        $this->defaultOrderDirection = $defaultOrderDirection;
        return $this;
    }
}