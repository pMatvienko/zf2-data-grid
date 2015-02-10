<?php
namespace DataGrid\View\Helper;

use DataGrid\CellSet;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\View\Helper\AbstractHelper;

class GridRowHelper extends AbstractHelper implements \Zend\ServiceManager\ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Invoke as function
     *
     * @param CellSet $сellSet
     * @return $this|void
     * @internal param \DataGrid\CellSet $grid
     */
    public function __invoke(CellSet $сellSet)
    {
        if (!$сellSet) {
            return $this;
        }

        return $this->render($сellSet);
    }

    public function render(CellSet $сellSet)
    {
        $cells = '';
        foreach ($сellSet as $index => $cell) {
            $cells .= $this->getCellHelper($cell)->render($cell);
        }


        return '<tr>' . $cells . '</tr>';
    }

    /**
     * Retrieve the FormElement helper
     *
     * @return FormElement
     */
    public function getCellHelper($cell)
    {
        $renderer = lcfirst(str_replace('\\', '', get_class($cell)));
        if (method_exists($this->view, 'plugin')) {
            $helper = $this->view->plugin($renderer);
        }
        return $helper;
    }
}