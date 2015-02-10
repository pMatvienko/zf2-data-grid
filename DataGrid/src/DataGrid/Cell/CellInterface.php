<?php
namespace DataGrid\Cell;

interface CellInterface
{
    public function getAttrib($name);
    public function setAttrib($name, $value);
    public function getAttribs();
    public function setAttribs($attribs);
    public function setLabel($label);
    public function getLabel();
    public function setContent($content);
    public function getContent();
    public function render();
    public function getContentVariables($withModifiers = false);
    public function setData($data);
    public function getData($var = null);
    public function getModifiersByMask($mask);
    public function setAvailabilityCheck($check);
    public function isAvailable();
    public function getName();
    public function setName($name);
    public function getId();
    public function setId($id);
    public function setOrderBy($orderBy);
    public function getOrderBy();
    public function getCurrentOrderDirection();
}