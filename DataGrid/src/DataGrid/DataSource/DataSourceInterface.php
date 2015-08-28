<?php
namespace DataGrid\DataSource;

use DataGrid\CellSet;

interface DataSourceInterface extends \Countable, \Iterator, \ArrayAccess
{
    public function setSource($source);
    public function setMaxResults($maxResults);
    public function getMaxResults();
    public function bindCellsset(CellSet $cellsset);
    public function getCellssetDecorator();
}