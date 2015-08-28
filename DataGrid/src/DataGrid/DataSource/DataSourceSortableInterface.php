<?php
namespace DataGrid\DataSource;

interface DataSourceSortableInterface
{
    public function addOrderBy($field, $direction);
}