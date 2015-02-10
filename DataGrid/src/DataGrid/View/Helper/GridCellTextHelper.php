<?php
namespace DataGrid\View\Helper;

use DataGrid\Cell\CellInterface as CellInterface;
use DataGrid\Cell\Text as TextCell;

class GridCellTextHelper extends GridCellAbstractHelper
{
    /**
     * @param CellInterface $cell
     * @return null|string
     * @throws \DataGrid\Exception
     */
    public function getCellContent(CellInterface $cell)
    {
        if(!($cell instanceof TextCell)) {
            throw new \DataGrid\Exception('"GridCellText" helper can render only text column type');
        }
        if($cell->isAvailable()) {
            return $cell->render();
        }
        return '';
    }
}