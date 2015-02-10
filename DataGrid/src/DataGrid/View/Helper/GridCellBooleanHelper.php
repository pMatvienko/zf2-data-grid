<?php
namespace DataGrid\View\Helper;

use DataGrid\Cell\CellInterface as CellInterface;
use DataGrid\Cell\Boolean as BooleanCell;
use Zend\Form\Element\Checkbox;

class GridCellBooleanHelper extends GridCellAbstractHelper
{

    /**
     * @param CellInterface $cell
     * @return string
     * @throws \DataGrid\Exception
     */
    public function getCellContent(CellInterface $cell)
    {
        if(!($cell instanceof BooleanCell)) {
            throw new \DataGrid\Exception('"GridCellBoolean" helper can render only boolean column type');
        }
        if(!$cell->isAvailable()) {
            return '';
        }
        $element = new Checkbox($cell->getName());

        $attribs = $this->extractAttributes($cell);
        if(!empty($attribs[self::ATTRIBS_ELEMENT])){
            $element->setAttributes($attribs[self::ATTRIBS_ELEMENT]);
        }

        if($cell->render()) {
            $element->setAttribute('checked', 'checked');
        }
        return $this->getView()->formElement($element);
    }
}