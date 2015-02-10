<?php
namespace DataGrid\View\Helper;

use DataGrid\DataGrid;
use Zend\View\Helper\AbstractHelper;

class GridHelper extends AbstractHelper
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
        $gridTableContent = $this->getHelper('dataGridTable')->render($grid);
        $pagination = '';
        if(null != $grid->getPaginator()){
            $pagination = $this->getHelper('dataGridPagination')->render($grid->getPaginator());
        }
        return '<div class="sbx-grid">'
        . $pagination
        . $gridTableContent
        . $pagination
        . '</div>';
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