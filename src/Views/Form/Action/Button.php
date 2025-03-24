<?php

namespace RocketPhp\RocketUI\Views\Form\Action;

use RocketPhp\RocketUI\Views\Form\Action\Abstract\AbstractAction;

class Button extends AbstractAction
{
    private ?string $type;
    public function __construct(\DOMElement $node)
    {
        parent::__construct($node);
        $this->type = $node->getAttribute('type');
    }

    public function getType() : ?string
    {
        return $this->type;
    }

    public function omitJson(mixed $data): ?array
    {
        return [
            'type' => $this->getType()
        ];
    }
}