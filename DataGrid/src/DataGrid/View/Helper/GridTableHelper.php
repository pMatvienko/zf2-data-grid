<?php
namespace DataGrid\View\Helper;

use DataGrid\CellSet;
use DataGrid\DataGrid;
use Zend\I18n\View\Helper\AbstractTranslatorHelper;

class GridTableHelper extends AbstractTranslatorHelper
{
    /**
     * Invoke as function
     *
     * @param DataGrid $grid
     * @return $this|void
     */
    public function __invoke(DataGrid $grid)
    {
        if (!$grid) {
            return $this;
        }

        return $this->render($grid);
    }

    public function render(DataGrid $grid)
    {
        $cellset = $grid->getCells();
        $dataSource = $grid->getDataSource();


        if(count($dataSource) > 0){
            $visibility = $this->calculateVisibleColumns($grid);
            foreach($visibility as $index => $isVisible){
                if(!$isVisible){
                    $cellset->removeCell($index);
                }
            }
            $rows = '';
            foreach($dataSource as $item){
                $rows .= $this->getHelper('dataGridRow')->render($item);
            }
        } else {
            $label = 'sbx-grid:no-records-found';
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }
            $rows = '<tr><td colspan="' . count($cellset) . '"><div class="no-records-found">' . $label . '</div></td></tr>';
        }

        $head = $this->getHelper('dataGridHead')->render($cellset);

        return '<table celpadding="0" cellspacing="0" class="table table-bordered table-striped table-hover">' .
        $head
        .'<tbody>'
        . $rows
        . '</tbody>'
        . '</table>';
    }

    public function calculateVisibleColumns(DataGrid $grid)
    {
        $cellset = $grid->getCells();
        $visibility = $this->getinitialCellsVisibility($cellset);

        foreach($grid->getDataSource() as $row) {
            /**
             * @var \DataGrid\Cell\Cell $cell
             */
            foreach($row as $index => $cell)
            {
                if($cell->isAvailable()) {
                    $visibility[$index] = true;
                }
            }
            if(!in_array(false, $visibility)) {
                break;
            }
        }
        return $visibility;
    }

    private function getinitialCellsVisibility(CellSet $cellset)
    {
        $cellsVisibility = array();
        foreach($cellset as $index=>$cell){
            $cellsVisibility[$index] = false;
        }
        return $cellsVisibility;
    }

    /**
     * Retrieve the FormElement helper
     *
     * @return FormElement
     */
    protected function getHelper($helperName)
    {
        if (method_exists($this->view, 'plugin')) {
            $helper = $this->view->plugin($helperName);
        }
        return $helper;
    }
}