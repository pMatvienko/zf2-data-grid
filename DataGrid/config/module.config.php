<?php
return [
    'service_manager' => [
        'invokables' => [
            'DataGrid/DataSource/Factory' => 'DataGrid\DataSource\Factory',
            'DataGrid/Cell/Factory' => 'DataGrid\Cell\Factory',
        ],
    ],
    'view_helpers'    => [
        'invokables' => [
            'dataGrid'       => 'DataGrid\View\Helper\GridHelper',
            'dataGridPagination'       => 'DataGrid\View\Helper\GridPaginatorHelper',
            'dataGridTable'       => 'DataGrid\View\Helper\GridTableHelper',
            'dataGridHead'       => 'DataGrid\View\Helper\GridHeadHelper',
            'dataGridHeadText'       => 'DataGrid\View\Helper\GridHeadTextHelper',
            'dataGridHeadOrder'       => 'DataGrid\View\Helper\GridHeadOrderHelper',
            'dataGridRow'       => 'DataGrid\View\Helper\GridRowHelper',
            'dataGridCellText'       => 'DataGrid\View\Helper\GridCellTextHelper',
            'dataGridCellBoolean'       => 'DataGrid\View\Helper\GridCellBooleanHelper',
            'dataGridCellUnion'       => 'DataGrid\View\Helper\GridCellUnionHelper',
            'dataGridCellAction'       => 'DataGrid\View\Helper\GridCellActionHelper',
        ],
    ],
];
