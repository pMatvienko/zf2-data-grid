<?php
namespace DataGrid;

use DataGrid\DataSource\DataSourceInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DataGrid
{
    /**
     * @var ServiceLocatorInterface
     */
    private static $serviceLocator = null;

    /**
     * @param $serviceLocator
     */
    public static function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        self::$serviceLocator = $serviceLocator;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return self::$serviceLocator;
    }

    /**
     * @var DataSourceInterface
     */
    private $dataSource;
    private $cells;
    private $paginator = null;
    private $id = '';
    private $defaultOrderField = null;
    private $defaultOrderDirection = null;
    private $filter = null;

    /**
     * Wrapper for getFilter()->addItem($definetion, $name);
     *
     * @param $definetion
     * @param null $name
     *
     * @return $this
     */
    public function addFilterItem($definetion, $name = null)
    {
        $this->getFilter()->addItem($definetion, $name);
        return $this;
    }

    /**
     * Gets a filter instance from grid. If filter not set, then creating an empty filter instance.
     *
     * @return Filter
     */
    public function getFilter()
    {
        if (null == $this->filter) {
            $this->filter = new Filter([
                'serviceLocator' => $this->getServiceLocator(),
                'id'             => $this->getId(),
            ]);
        }

        return $this->filter;
    }

    /**
     * Checking is grid have a filter instance.
     *
     * @return bool
     */
    public function hasFilter()
    {
        return $this->filter != null;
    }

    /**
     * Wrapper for getCells()->appendCell($definition, $name). Will add cell to the end of set.
     *
     * @param array definition Cell definition.
     * @param null $name
     *
     * @return $this
     */
    public function appendCell($definition, $name = null)
    {
        $this->getCells()->appendCell($definition, $name);
        return $this;
    }

    /**
     * Wrapper for getCells()->prependCell($definition, $name). will add cell to the beginning of set.
     *
     * @param array definition Cell definition
     * @param null $name
     *
     * @return $this
     */
    public function prependCell($definition, $name = null)
    {
        $this->getCells()->prependCell($definition, $name);
        return $this;
    }

    /**
     * Gets a cells set from grid. If not set creating an empty cells set
     *
     * @return CellSet
     */
    public function getCells()
    {
        if ($this->cells == null) {
            $this->cells = new CellSet([
                'serviceLocator' => $this->getServiceLocator(),
                'id'             => $this->getId(),
            ]);
        }
        return $this->cells;
    }

    /**
     * Gets a data source provided for grid. This function apply CellsSet, sorting, filter and paginator to source
     *
     * @return DataSourceInterface
     */
    public function getDataSource()
    {
        $this->dataSource->bindCellsset($this->getCells());

        if (null != $this->getDefaultOrderDirection() && null != $this->getDefaultOrderField() && ($this->dataSource instanceof \DataGrid\DataSource\DataSourceSortableInterface)) {
            $this->dataSource->addOrderBy($this->getDefaultOrderField(), $this->getDefaultOrderDirection());
        }

        if ($this->hasFilter()) {
            $this->getFilter()->applyTo($this->dataSource);
        }

        if (null != $this->getPaginator()) {
            $this->getPaginator()->applyTo($this->dataSource);
        }
        return $this->dataSource;
    }

    /**
     * Sets a data source for grid.
     *
     * @param mixed $dataSource
     *
     * @return $this
     */
    public function setDataSource($dataSource)
    {
        if (!$dataSource instanceof DataSourceInterface) {
            $dataSource = $this->getServiceLocator()->get('DataGrid\Factory\DataSource')->get($dataSource);
        }
        $this->dataSource = $dataSource;
        return $this;
    }

    /**
     * Gets pagination.
     *
     * @return \DataGrid\Paginator
     */
    public function getPaginator()
    {
        if (!empty($this->paginator)) {
            $this->paginator->setId($this->getId());
        }
        return $this->paginator;
    }

    /**
     * Sets pagination.
     *
     * @param array|Paginator $paginator Paginator definition or instance.
     *
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
     * Gets grid id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets grid id. Ensure that your ids are unique.
     *
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Gets default sorting field.
     *
     * @return string|null
     */
    public function getDefaultOrderField()
    {
        return $this->defaultOrderField;
    }

    /**
     * Gets default sorting direction.
     *
     * @return string|null
     */
    public function getDefaultOrderDirection()
    {
        return $this->defaultOrderDirection;
    }

    /**
     * Set default sorting field and direction. Ensure that your data source contains that field.
     *
     * @param string $defaultOrderField     Field to sort by.
     * @param string $defaultOrderDirection Sort direction. Possible values are "ASC" AND "DESC".
     *
     * @return $this
     */
    public function setDefaultOrder($defaultOrderField, $defaultOrderDirection = 'ASC')
    {
        $this->defaultOrderField = $defaultOrderField;
        $this->defaultOrderDirection = $defaultOrderDirection;
        return $this;
    }
}