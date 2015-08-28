<?php
namespace DataGrid\View\Helper\GridFilter;

use DataGrid\Filter\Item\Search;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class SearchHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @param Search|null $item
     * @return $this|string
     */
    public function __invoke(Search $item=null)
    {
        if (!$item) {
            return $this;
        }
        return $this->render($item);
    }

    public function render(Search $item)
    {
        return '
            <div class="input-group">
                <input type="text" class="form-control" placeholder="' . $item->getLabel() . '" name="' . $item->getName() . '" value="' . $item->getValue() . '">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit">
                        <i class="glyphicon glyphicon-search"></i>
                    </button>
                </span>
            </div>
        ';
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