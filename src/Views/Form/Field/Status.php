<?php

namespace RocketPhp\RocketUI\Views\Form\Field;

use RocketPhp\RocketUI\Views\Form\Field\Abstract\AbstractField;

class Status extends AbstractField
{
    private ?string $history;

    public function __construct(\DOMElement $node)
    {
        parent::__construct($node);
        $this->history = $node->getAttribute('history');
    }

    public function getHistory(): ?string
    {
        return $this->history;
    }

    public function omitJson(mixed $data): ?array
    {
        return [
            'history' => $this->getHistory(),
        ];
    }
}
