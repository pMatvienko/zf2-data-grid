<?php
namespace DataGrid\DataSource;

interface DataSourcePaginatedInterface
{
    public function setPage($page);
    public function getPage();
}