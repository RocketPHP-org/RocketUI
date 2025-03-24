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

    }

    public function buildForm(Form $viewDefinition, mixed $data): string
    {
        return $viewDefinition->buildForm($data);
    }

    public function buildGrid(Grid $viewDefinition, mixed $data)
    {

        return $viewDefinition->buildGrid($data);
    }

}