# Sandbox DataGrid

Данный компонент предназначен для релизации функционала по отрисовке разного рода таблиц





## ячейка

Параметры принимаемые ячейками
- _type_ - тип ячейки, используется при создании через фабрику
- _content_ - Содержимое Ячейки
- _label_ - Имя колонки

Пример добавления ячейки

```
    $grid = new DataGrid\DataGrid();
    $grid
        ->appendCell(
            array(
                    'type' => 'boolean',
                    'content' => 'isPublic',
                    'label' => 'is_public',
                    'attribs' => array(
                        'cell:style' => 'width:45px; text-align:center;',
                        'element:disabled' => 'disabled',
                        'element:name' => 'resource_is_enabled[{$id}]'
                    ),
                    'availabilityCheck' => '{$type} != mvc'
                )
        , 'is_public')
        >appendCell(
            array(
                    'type' => 'text',
                    'content' => '<b>{$module}</b>: {$resource}',
                    'label' => 'resource',
                    'attribs' => array(
                        'cell:style' => 'width:45px; text-align:center;',
                        'element:disabled' => 'disabled',
                        'element:name' => 'resource_is_enabled[{$id}]'
                    ),
                    'availabilityCheck' => function($data){
                        return $data['type'] != 'mvc';
                    }
                )
        );
```
