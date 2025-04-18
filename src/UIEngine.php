<?php

namespace RocketPhp\RocketUI;

use RocketPhp\RocketUI\Adapter\RjsfSchemaBuilder;
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

    public function buildRJSFSchema(Form $viewDefinition, mixed $data): string
    {
        $schema = $this->buildForm($viewDefinition, $data);
        $schemaArray = json_decode($schema, true);
        $builder = new RjsfSchemaBuilder();
        $array = $builder->convert($schemaArray);
        $json = json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $json;
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