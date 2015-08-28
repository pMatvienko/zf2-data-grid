<?php
return [
    'service_manager' => [
        'invokables' => [
            'DataGrid\Factory\DataSource'  => 'DataGrid\DataSource\Factory',
            'DataGrid\Factory\Cell'        => 'DataGrid\Cell\Factory',
            'DataGrid\Factory\FilterItem'  => 'DataGrid\Filter\Item\Factory',
            'DataGrid\DataSource\Doctrine' => 'DataGrid\DataSource\DoctrineSource',
            'DataGrid\DataSource\Array'    => 'DataGrid\DataSource\ArraySource',

            'DataGrid\Cell\Text' => '\DataGrid\Cell\Text',
            'DataGrid\Cell\Boolean' => '\DataGrid\Cell\Boolean',
            'DataGrid\Cell\Union' => '\DataGrid\Cell\Union',
            'DataGrid\Cell\Action' => '\DataGrid\Cell\Action',

            'DataGrid\Filter\Search' => '\DataGrid\Filter\Item\Search',
        ],
        'shared'     => [
            'DataGrid\DataSource\Doctrine' => false,
            'DataGrid\DataSource\Array'    => false,
            'DataGrid\Cell\Text' => false,
            'DataGrid\Cell\Boolean' => false,
            'DataGrid\Cell\Union' => false,
            'DataGrid\Cell\Action' => false,
        ],
    ],
    'view_helpers'    => [
        'invokables' => [
            'dataGrid'             => 'DataGrid\View\Helper\GridHelper',
            'dataGridPagination'   => 'DataGrid\View\Helper\GridPaginatorHelper',
            'dataGridFilter'       => 'DataGrid\View\Helper\GridFilterHelper',
            'dataGridFilterSearch' => 'DataGrid\View\Helper\GridFilter\SearchHelper',
            'dataGridTable'        => 'DataGrid\View\Helper\GridTableHelper',
            'dataGridHead'         => 'DataGrid\View\Helper\GridHeadHelper',
            'dataGridHeadText'     => 'DataGrid\View\Helper\GridHeadTextHelper',
            'dataGridHeadOrder'    => 'DataGrid\View\Helper\GridHeadOrderHelper',
            'dataGridRow'          => 'DataGrid\View\Helper\GridRowHelper',
            'dataGridCellText'     => 'DataGrid\View\Helper\GridCellTextHelper',
            'dataGridCellBoolean'  => 'DataGrid\View\Helper\GridCellBooleanHelper',
            'dataGridCellUnion'    => 'DataGrid\View\Helper\GridCellUnionHelper',
            'dataGridCellAction'   => 'DataGrid\View\Helper\GridCellActionHelper',
        ],
    ],
];
