<?php
namespace DataGrid\View\Helper;

use DataGrid\Filter;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class GridFilterHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @param Filter|null $filter
     * @return $this|string
     */
    public function __invoke(Filter $filter=null)
    {
        if (!$filter) {
            return $this;
        }
        return $this->render($filter);
    }

    public function render(Filter $filter)
    {
        $searchbox = $filter->getSearchBox();

        $searchboxHtml = '';
        if(null != $searchbox){
            $searchboxHtml = $this->getHelper('dataGridFilterSearch')->render($searchbox);
        }

        return '<div ' . $filter->compileAttributesString() . '><form method="GET">'.
        $searchboxHtml
        .'</form></div>';
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