# zf2-data-grid


## Usage example

<?php
namespace UKEAS\Grid\Institute;

use \DataGrid\DataGrid;

class Common extends DataGrid
{
    public function __construct()
    {
        $this->setId('institute');
        $this->setDefaultOrder('id', 'asc');

        $this->addFilterItem([
            'type' => 'Search',
            'label' => 'Find university',
            'fields' => [
                'id' => 'eq',
                'swbuid' => 'contains',
                'name' => 'contains',
                'country._name' => 'contains',
                'country._namespace' => 'eq'
            ]
        ]);


        $this
            ->appendCell(array(
                'type' => 'text',
                'content' => '{$id}',
                'label' => 'Id',
                'orderBy' => 'id',
                'attribs' => array(
                    'cell:style' => 'width:65px; text-align:center;',
                ),
            ), 'identifier')
            ->appendCell(array(
                'type' => 'text',
                'content' => '{$swbuid}',
                'label' => 'Swbuid',
                'orderBy' => 'swbuid',
            ), 'swbuid')
            ->appendCell(array(
                'type' => 'text',
                'content' => '<b>({$country._namespace})</b> {$country._name}',
                'label' => 'Country name',
                'orderBy' => 'country',
            ), 'countryName')
            ->appendCell(array(
                'type' => 'text',
                'content' => '{$name}',
                'label' => 'Form title',
            ), 'name')
            ->appendCell(array(
                'type' => 'text',
                'content' => '{$qsRank}',
                'label' => 'QsRank',
            ), 'QsRank')
            ->appendCell(array(
                'type' => 'boolean',
                'content' => 'introHistory',
                'label' => 'Intro history',
                'attribs' => array(
                    'cell:style'       => 'width:120px; text-align:center;',
                    'element:disabled' => 'disabled',
                    'element:name'     => 'introHistory[{$id}]'
                ),
            ), 'introHistory')
            ->appendCell(array(
                'type' => 'boolean',
                'content' => 'accomodation',
                'label' => 'Accomodation',
                'attribs' => array(
                    'cell:style'       => 'width:120px; text-align:center;',
                    'element:disabled' => 'disabled',
                    'element:name'     => 'accomodation[{$id}]'
                ),
            ), 'accomodation')
            ->appendCell(array(
                'type' => 'boolean',
                'content' => 'ranking',
                'label' => 'Ranking',
                'attribs' => array(
                    'cell:style'       => 'width:120px; text-align:center;',
                    'element:disabled' => 'disabled',
                    'element:name'     => 'ranking[{$id}]'
                ),
            ), 'ranking')
            ->appendCell(array(
                'type' => 'boolean',
                'content' => 'internationalStudents',
                'label' => 'International students',
                'attribs' => array(
                    'cell:style'       => 'width:120px; text-align:center;',
                    'element:disabled' => 'disabled',
                    'element:name'     => 'internationalStudents[{$id}]'
                ),
            ), 'internationalStudents')
            ->appendCell(array(
                'type'    => 'union',
                'label'   => '',
                'joinBy'  => '&nbsp;&nbsp;',
                'attribs' => array(
                    'cell:style' => 'width:55px; text-align:center;',
                ),
                'content' => array(
                    array(
                        'type'    => 'action',
                        'label'   => 'Edit',
                        'content' => array(
                            'action' => 'edit',
                            'id'     => '{$id}'
                        ),
                        'attribs' => array(
                            'element:class' => 'glyphicon glyphicon-edit',
                            'element:title' => 'Edit'
                        )
                    ),
                    array(
                        'type'    => 'action',
                        'label'   => '{$country._namespace}',
                        'content' => array(
                            'action' => 'edit',
                            'id'     => '{$specificUs.id}',
                            'entity' => 'institute_{$country._namespace}'
                        ),
                        'attribs' => array(
                            'element:class' => 'glyphicon glyphicon-education',
                            'element:title' => 'Edit Specific'
                        ),
                        'availabilityCheck' => function($params){
                            return strtolower($params['country._namespace']) == 'us';
                        },
                    ),
                )
            ));

        $this->setPaginator(array(
            'pageSize' => 50,
        ));
    }
}
