<?php
namespace DataGrid\View\Helper;

use DataGrid\Cell\CellInterface;
use Zend\I18n\View\Helper\AbstractTranslatorHelper;

class GridHeadTextHelper extends AbstractTranslatorHelper
{
    /**
     * Invoke as function
     *
     * @param CellInterface $cell
     * @return $this|string
     */
    public function __invoke(CellInterface $cell)
    {
        if (!$cell) {
            return $this;
        }

        return $this->render($cell);
    }

    public function render(CellInterface $cell)
    {
        $label = $cell->getLabel();
        if (null !== ($translator = $this->getTranslator())) {
            $label = $translator->translate(
                $label, $this->getTranslatorTextDomain()
            );
        }
        return '<th>' . $label . '</th>';
    }
}