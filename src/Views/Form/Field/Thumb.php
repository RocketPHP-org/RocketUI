<?php

namespace RocketPhp\RocketUI\Views\Form\Field;

use RocketPhp\RocketUI\Views\Form\Field\Abstract\AbstractField;

class Thumb extends AbstractField
{
    public function __construct(\DOMElement $node)
    {
        parent::__construct($node);
    }

    public function omitJson(mixed $data): ?array
    {
        return [];
    }
}
