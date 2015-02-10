<?php
namespace DataGrid\View\Helper;

use Zend\I18n\View\Helper\AbstractTranslatorHelper;
use DataGrid\Cell\CellInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class GridHeadOrderHelper extends AbstractTranslatorHelper implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
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

        $currentDirection = $cell->getCurrentOrderDirection();

        switch($currentDirection){
            case 'asc':
            case 'ASC':
                $setDirection = 'desc';
                $labelAddon = '<span class="caret caret-reversed">';
                break;
            case 'desc':
            case 'DESC':
                $setDirection = null;
                $labelAddon = '<span class="caret">';
                break;
            default:
                $setDirection = 'asc';
                $labelAddon = '';
        }

        $content = '<a href="' . $this->assembleOrderUrl($cell->getName(), $setDirection, $cell->getId()) . '">' . $label . $labelAddon . '</a>';
        return '<th>' . $content . '</th>';
    }

    private function assembleOrderUrl($cellName, $direction=null, $prefix='')
    {
        $queryParams = $this->getServiceLocator()->getServiceLocator()->get('Request')->getQuery()->toArray();

        $queryParams[$prefix.'order-'.$cellName] = $direction;
        if(empty($direction)) {
            unset($queryParams[$prefix.'order-'.$cellName]);
        }

        return $this->getView()->url(
            null,
            array(),
            array(
                'query' => $queryParams
            ),
            true
        );
    }
}