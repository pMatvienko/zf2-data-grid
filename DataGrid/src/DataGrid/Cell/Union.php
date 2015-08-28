<?php
namespace DataGrid\Cell;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class Union extends Cell implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    private $joinBy = null;

    /**
     * @return null
     */
    public function getJoinBy()
    {
        return $this->joinBy;
    }

    /**
     * @param null $joinBy
     * @return $this
     */
    public function setJoinBy($joinBy)
    {
        $this->joinBy = $joinBy;
        return $this;
    }

    public function getContent()
    {
        foreach($this->content as $index => $cell){
            $cell->setData($this->getData());
        }
        return $this->content;
    }

    protected function renderContent()
    {
        $content = array();
        /**
         * @var \DataGrid\Cell\Cell $cell
         */
        foreach($this->content as $index=>$cell){
            $content[] = $cell->setData($this->getData())->render();
        }
        return implode($this->getJoinBy(), $content);
    }

    public function isAvailable()
    {
        /**
         * @var \DataGrid\Cell\Cell $cell
         */
        foreach($this->content as $index=>$cell){
            if($cell->setData($this->getData())->isAvailable()){
                return true;
            }
        }
        return false;
    }

    /**
     * @param $content
     * @return $this
     * @throws Exception
     */
    public function setContent($content)
    {
        if(!is_array($content)){
            throw new Exception('Content for union column should be array of other columns or their definations');
        }
        foreach($content as $index => $cell){
            if(is_array($cell)){
                $content[$index] = $this->getServiceLocator()->get('DataGrid\Factory\Cell')->get($cell);
            }
        }
        $this->content = $content;
        return $this;
    }
    public function getContentVariables($withModifiers = false)
    {
        $variables = array();
        if(is_array($this->content)){
            foreach($this->content as $cell){
                $variables += $cell->getContentVariables($withModifiers);
            }
        }
        return $variables;
    }
}