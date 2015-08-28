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
        $filter = '';
        if(null != $grid->getPaginator()){
            $pagination = $this->getHelper('dataGridPagination')->render($grid->getPaginator());
        }
        if($grid->hasFilter()){
            $filter = '<div class="col-md-6 col-lg-6 col-xs-12 sbx-grid-filter">' . $this->getHelper('dataGridFilter')->render($grid->getFilter()) . '</div>';
        }
        if('' != $filter){
            $additionalClass = '';
        } else {
            $additionalClass = 'col-md-offset-6 col-lg-offset-6';
        }

        $topPpagination = '<div class="' . $additionalClass . ' col-md-6 col-lg-6 col-xs-12 sbx-grid-paginator">'.$pagination.'</div>';
        $pagination = '<div class="col-md-offset-6 col-lg-offset-6 col-md-6 col-lg-6 col-xs-12 sbx-grid-paginator">'.$pagination.'</div>';

        return '<div class="sbx-grid">'
        . '<div class="row">' . $filter . $topPpagination . '</div>'
        . $gridTableContent
        . '<div class="row">' . $pagination . '</div>'
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