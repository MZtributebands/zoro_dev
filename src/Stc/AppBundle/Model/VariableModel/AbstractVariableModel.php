<?php

namespace Stc\AppBundle\Model\VariableModel;

abstract class AbstractVariableModel
{
    protected $vars = [];

    abstract public function setVars($vars = array());

    abstract public function getVars();
}