<?php
namespace DataGrid\Cell;

use DataGrid\Condition\Pair;
use DataGrid\Condition\PublicInterface;
use DataGrid\Modifier\Base as BaseModifier;

abstract class Cell implements CellInterface
{

    const MODIFIER_SPLITTER = ':|';
    const DEFAULT_DATETIME_MASK = 'dd MMMM, YYYY; HH:mm';

    private $attribs = array();
    private $label = null;
    protected $content = null;
    private $data = null;
    private $avilabilityChecker = null;
    private $name = '';
    private $id = '';
    private $orderBy = null;


    /**
     * @param $options
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    public function setOptions($options)
    {
        foreach ($options as $k => $v) {
            $k = 'set' . ucfirst($k);
            if (method_exists($this, $k)) {
                $this->$k($v);
            }
        }
    }

    public function getAttrib($name)
    {
        $value = array_key_exists($name, $this->attribs) ? $this->attribs[$name] : null;
        if (preg_match('/\{\$([^}]+)\}/', $value, $matches)) {
            $value = str_replace($matches[0], $this->getData($matches[1]), $value);
        }
        return $value;
    }

    public function getAttribs()
    {
        $out = array();
        foreach ($this->attribs as $name => $val) {
            $out[$name] = $this->getAttrib($name);
        }
        return $out;
    }

    public function setAttribs($attribs)
    {
        $this->attribs = array();
        foreach ($attribs as $attr => $val) {
            $this->setAttrib($attr, $val);
        }
    }

    public function setAttrib($name, $value)
    {
        $this->attribs[$name] = $value;
        return $this;
    }

    /**
     * @param $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    public function render()
    {
        return ($this->isAvailable()) ? $this->renderContent() : null;
    }

    abstract protected function renderContent();

    public function getContentVariables($withModifiers = false, $content=null)
    {
        $content = (null == $content) ? $this->content. implode($this->attribs) : $content;

        $variables = array();
        preg_match_all('/\{\$([^}]+)\}/', $content, $variables);
        $out = array();
        foreach ($variables[0] as $key => $mask) {
            if (!$withModifiers) {
                $var = explode(self::MODIFIER_SPLITTER, $variables[1][$key]);
                $var = $var[0];
            } else {
                $var = $variables[1][$key];
            }

            $out[$mask] = $var;
        }
        return $out;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getData($var = null)
    {
        if (null == $var) {
            return $this->data;
        }
        $varParts = explode(self::MODIFIER_SPLITTER, $var);
        if (!array_key_exists($varParts[0], $this->data)) {
            return null;
        }
        $value = $this->data[$varParts[0]];
        unset($varParts[0]);

        foreach ($varParts as $modify) {
            $value = BaseModifier::factory($modify)->modify($value);
        }
        return $value;
    }


    public function getModifiersByMask($mask)
    {
        $mask = explode(self::MODIFIER_SPLITTER, $mask);
    }

    public function setAvailabilityCheck($check)
    {
        if (is_string($check)) {
            $this->avilabilityChecker = new Pair($check);
        } else {
            $this->avilabilityChecker = $check;
        }
        return $this;
    }

    public function isAvailable()
    {
        if (null == $this->avilabilityChecker) {
            return true;
        } elseif ($this->avilabilityChecker instanceof PublicInterface) {
            return $this->avilabilityChecker->calculate($this->getData());
        } else {
            $checker = $this->avilabilityChecker;
            return $checker($this->getData());
        }
    }

    public function __toString()
    {
        return $this->render();
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = strval($name);
        return $this;
    }

    /**
     * @return null
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param null $orderBy
     * @return $this
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getCurrentOrderDirection()
    {
        if (empty($_GET[$this->getId() . 'order-' . $this->getName()])) {
            return null;
        }
        return $_GET[$this->getId() . 'order-' . $this->getName()];
    }
}