<?php

namespace RocketPhp\RocketUI\Views\Form\Field;
use RocketPhp\RocketUI\Views\Form\Field\Abstract\AbstractField;

class Input extends AbstractField
{
    private ?string $type;
    private ?string $min;
    private ?string $max;
    private ?string $format;
    private ?string $currency;

    public function __construct(\DOMElement $node)
    {
        parent::__construct($node);
        $this->type = $node->getAttribute('type');
        $this->min = $node->getAttribute('min');
        $this->max = $node->getAttribute('max');
        $this->format = $node->getAttribute('format');
        $this->currency = $node->getAttribute('currency');
    }

    public function getType() : ?string
    {
        return $this->type;
    }
    public function getMin() : ?string
    {
        return $this->min;
    }
    public function getMax() : ?string
    {
        return $this->max;
    }
    public function getFormat() : ?string
    {
        return $this->format;
    }
    public function getCurrency() : ?string
    {
        return $this->currency;
    }

}