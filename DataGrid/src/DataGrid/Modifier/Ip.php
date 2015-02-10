<?php
namespace DataGrid\Modifier;

class Ip extends Base
{
    public function modify($value)
    {
        return long2ip($value);
    }
}