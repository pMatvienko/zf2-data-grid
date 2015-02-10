<?php
namespace DataGrid\View\Helper;

use DataGrid\Cell\CellInterface as CellInterface;
use DataGrid\Cell\Action as ActionCell;

class GridCellActionHelper extends GridCellAbstractHelper
{
    /**
     * @param CellInterface $cell
     * @return null|string
     * @throws \DataGrid\Exception
     */
    public function getCellContent(CellInterface $cell)
    {
        if(!($cell instanceof ActionCell)) {
            throw new \DataGrid\Exception('"GridCellAction" helper can render only action column type');
        }

        $attribs = $this->extractAttributes($cell);
        $content = '';
        $textLabel = $cell->getTextLabel();
        if ($textLabel != '' && null !== ($translator = $this->getTranslator())) {
            $textLabel = $translator->translate(
                $textLabel, $this->getTranslatorTextDomain()
            );
        }
        if($cell->isAvailable()) {
            $attribs[self::ATTRIBS_ELEMENT]['href'] = $cell->render();
            $content = '<a' . (!empty($attribs[self::ATTRIBS_ELEMENT]) ? ' ' . $this->assembleAttributes($attribs[self::ATTRIBS_ELEMENT]) : '') . '>' . $textLabel . '</a>';
        }
        return $content;
    }
}