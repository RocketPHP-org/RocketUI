<?php

namespace RocketPhp\RocketUI;

use RocketPhp\RocketUI\Views\Form\Field\Abstract\AbstractField;
use RocketPhp\RocketUI\Views\Form\Field\Input;
use RocketPhp\RocketUI\Views\Form\Form;
use RocketPhp\RocketUI\Views\Form\Layout\Container;
use RocketPhp\RocketUI\Views\Grid\Grid;

class UIEngine
{

    public function __construct()
    {
/*        if ($viewDefinition instanceof Form)
        {
            return $this->buildForm($viewDefinition, $data);

        }else if ($viewDefinition instanceof Grid)
        {
            return $this->buildGrid($viewDefinition, $data);
        }else{
            throw new \Exception("Invalid view definition");
        }*/

    }

    public function buildForm(Form $viewDefinition, mixed $data): string
    {
        return $viewDefinition->buildForm($data);
    }

    public function buildGrid(Grid $viewDefinition, mixed $data)
    {

    }

}