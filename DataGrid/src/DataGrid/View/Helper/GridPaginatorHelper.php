<?php
namespace DataGrid\View\Helper;

use DataGrid\Paginator;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class GridPaginatorHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    /**
     * Invoke as function
     *
     * @param Paginator $paginator
     * @return $this|void
     */
    public function __invoke(Paginator $paginator)
    {
        if (!$paginator) {
            return $this;
        }
        return $this->render($paginator);
    }

    public function render(Paginator $paginator)
    {
        $pagesCount = $paginator->getPagesCount();
        if($pagesCount < 2) {
            return '';
        }

        $startPage = $paginator->getPage() - $paginator->getVisiblePagesRange();

        if ($startPage < 1) {
            $startPage = 1;
        }
        $endPage = $startPage + ($paginator->getVisiblePagesRange() * 2);

        if ($endPage > $pagesCount) {
            $endPage = $pagesCount;
            $startPage = $endPage - ($paginator->getVisiblePagesRange() * 2);
            if ($startPage < 1) {
                $startPage = 1;
            }
        }

        $content = '';
        while($startPage <= $endPage) {
            if($startPage != $paginator->getPage()) {
                $content .= '<li><a href="' . $this->assemblePageUrl($startPage, $paginator->getId()) . '">' . $startPage . '</a></li>';
            } else {
                $content .= '<li class="active"><a href="' . $this->assemblePageUrl($startPage, $paginator->getId()) . '">' . $startPage . ' <span class="sr-only">(current)</span></a></li>';
            }
            ++$startPage;
        }

        if($paginator->getPage() > $paginator->getVisiblePagesRange()) {
            $content = '<li>
              <a href="' . $this->assemblePageUrl(1, $paginator->getId()) . '">
                <span aria-hidden="true">1</span>
              </a>
            </li>'
                . $content;
        }

        if($paginator->getPage() > 1) {
            $content = '<li>
              <a href="' . $this->assemblePageUrl($paginator->getPage()-1, $paginator->getId()) . '">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>'
                . $content;
        }


        if($paginator->getPage() < ($pagesCount-$paginator->getVisiblePagesRange())) {
            $content .= '<li>
              <a href="' . $this->assemblePageUrl($pagesCount, $paginator->getId()) . '" aria-label="Next">
                <span aria-hidden="true">'.$pagesCount.'</span>
              </a>
            </li>';
        }

        if($paginator->getPage() < $pagesCount) {
            $content .= '<li>
              <a href="' . $this->assemblePageUrl($paginator->getPage()+1, $paginator->getId()) . '" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>';
        }

        return '<ul class="pagination">'
        . $content
        . '</ul>';
    }

    private function assemblePageUrl($page, $prefix='')
    {
        $queryParams = $this->getServiceLocator()->getServiceLocator()->get('Request')->getQuery()->toArray();
        $queryParams[$prefix.'page'] = $page;

        return $this->getView()->url(
            null,
            array(),
            array(
                'query' => $queryParams
            ),
            true
        );
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