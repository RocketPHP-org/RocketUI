<?php

namespace RocketPhp\RocketUI\Views\Form\Action;

use RocketPhp\RocketUI\Views\Form\Action\Abstract\AbstractAction;

class Button extends AbstractAction
{
    private ?string $type;
    private ?string $confirm;
    public function __construct(\DOMElement $node)
    {
        parent::__construct($node);
        $this->type = $node->getAttribute('type');
        $this->confirm = $node->getAttribute('confirm');
    }

    public function getType() : ?string
    {
        return $this->type;
    }
    public function getConfirm() : ?string
    {
        return $this->confirm;
    }

    public function omitJson(mixed $data): ?array
    {
        return [
            'type' => $this->getType(),
            'confirm' => $this->getConfirm()
        ];
    }
}