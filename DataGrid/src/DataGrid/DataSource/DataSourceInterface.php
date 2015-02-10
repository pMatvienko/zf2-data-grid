<?php
namespace DataGrid\DataSource;

use DataGrid\CellSet;
use DataGrid\Paginator;

interface DataSourceInterface extends \Countable, \Iterator, \ArrayAccess
{
    public function setMaxResults($maxResults);
    public function getMaxResults();
    public function setPage($page);
    public function getPage();
    public function bindCellsset(CellSet $cellsset);
    public function getCellssetDecorator();
    public function addOrderBy($field, $direction);
    public function resetOrderBy();
    public function applyPaginator(Paginator $paginator);
}