<?php
namespace DataGrid\View\Helper;

use DataGrid\Cell\CellInterface as CellInterface;
use DataGrid\Cell\Union as UnionCell;

class GridCellUnionHelper extends GridCellAbstractHelper
{
    public function getCellContent(CellInterface $cell)
    {
        if(!($cell instanceof UnionCell)) {
            throw new \DataGrid\Exception('"GridCellBoolean" helper can render only boolean column type');
        }
        $content = array();
        foreach($cell->getContent() as $childCell){
            $content[] = $this->getRowHelper()->getCellHelper($childCell)->getCellContent($childCell);
        }
        return implode($cell->getJoinBy(), $content);
    }

    /**
     * Retrieve the FormElement helper
     *
     * @return \DataGrid\View\Helper\GridRowHelper
     */
    public function getRowHelper()
    {
        if (method_exists($this->view, 'plugin')) {
            return $this->view->plugin('dataGridRow');
        }
    }
}