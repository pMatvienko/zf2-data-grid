<?php
namespace DataGrid\View\Helper;

use DataGrid\Cell\CellInterface as CellInterface;
use Zend\I18n\View\Helper\AbstractTranslatorHelper;

abstract class GridCellAbstractHelper extends AbstractTranslatorHelper
{
    const ATTRIBS_ELEMENT = 'element';
    const ATTRIBS_CELL = 'cell';
    /**
     * Invoke as function
     *
     * @param CellInterface $cell
     * @return $this|void
     */
    public function __invoke(CellInterface $cell)
    {
        if (!$cell) {
            return $this;
        }
        return $this->render($cell);
    }

    /**
     * @param CellInterface $cell
     * @return null|string
     */
    abstract public function getCellContent(CellInterface $cell);

    /**
     * @param CellInterface $cell
     * @return string
     */
    public function render(CellInterface $cell)
    {
        $attribs = $this->extractAttributes($cell);
        return '<td' . (!empty($attribs[self::ATTRIBS_CELL]) ? ' ' . $this->assembleAttributes($attribs[self::ATTRIBS_CELL]) : '') . '>' . $this->getCellContent($cell) . '</td>';
    }

    public function assembleAttributes($attributes)
    {
        foreach($attributes as $name => $val){
            $attributes[$name] = $name . '="' . $val . '"';
        }
        return implode(' ', $attributes);
    }

    public function extractAttributes(CellInterface $cell)
    {
        $attribs = array();
        foreach($cell->getAttribs() as $name=>$val){
            if(!stristr($name, ':')){
                $attribs['general'][$name] = $val;
            } else {
                $name = explode(':', $name);
                $attribs[$name[0]][$name[1]] = $val;
            }
        }
        return $attribs;
    }
}