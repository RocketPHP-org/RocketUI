<?php

namespace RocketPhp\RocketUI\Views\Form\Field;

use RocketPhp\RocketUI\Views\Form\Field\Abstract\AbstractField;

class Header extends AbstractField
{
    private ?string $text;

    public function __construct(\DOMElement $node)
    {
        parent::__construct($node);
        $this->text = $node->getAttribute("text") ?: null;
    }
    public function getText() : ?string
    {
        return $this->text;
    }

    public function omitJson(mixed $data): ?array
    {
        return [
            'type' => 'header',
            'text' =>  $this->getText()
        ];

    }
}