<?php
namespace DataGrid\View\Helper;

use Zend\I18n\View\Helper\AbstractTranslatorHelper;

class GridHeadHelper extends AbstractTranslatorHelper
{
    /**
     * Invoke as function
     *
     * @param \DataGrid\CellSet $сellSet
     * @return $this|void
     * @internal param \DataGrid\CellSet $grid
     */
    public function __invoke(\DataGrid\CellSet $сellSet)
    {
        if (!$сellSet) {
            return $this;
        }

        return $this->render($сellSet);
    }

    public function render(\DataGrid\CellSet $сellSet)
    {
        $out = '<thead><tr>';
        /**
         * @var \DataGrid\Cell\Cell $cell
         */
        foreach($сellSet as $cell) {
            if($cell->getOrderBy() == null) {
                $renderer = $this->getHelper('dataGridHeadText');
            } else {
                $renderer = $this->getHelper('dataGridHeadOrder');
            }
            $out .= $renderer->render($cell);
        }
        $out .= '</tr></thead>';
        return $out;
    }

    /**
     * Retrieve the FormElement helper
     *
     * @return \DataGrid\View\Helper\GridRowHelper
     */
    public function getHelper($name)
    {
        if (method_exists($this->view, 'plugin')) {
            return $this->view->plugin($name);
        }
    }
}