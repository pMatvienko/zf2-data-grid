<?php
namespace DataGrid\DataSource;

interface DataSourceFilterableInterface
{
    const FILTER_TYPE_EQ = 'eq';
    const FILTER_YPE_CONTAINS = 'contains';

    public function orFilterGroup($fields, $joiner='OR');
    public function andFilterGroup($fields, $joiner='OR');
    public function andFilter($field, $value, $type = self::FILTER_TYPE_EQ);
    public function orFilter($field, $value, $type = self::FILTER_TYPE_EQ);
    public function clearFilters();
}