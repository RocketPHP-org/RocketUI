<?php

namespace RocketPhp\RocketUI\Views\Form\Field;

use RocketPhp\RocketUI\Views\Form\Field\Abstract\AbstractField;

class File extends AbstractField
{
    private ?string $accept;
    private bool $multiple;
    private ?int $maxSize;
    private ?string $type;
    private ?string $inputType;

    public function __construct(\DOMElement $node)
    {
        parent::__construct($node);
        $this->accept = $node->getAttribute("accept") ?: null;
        $this->multiple = $node->getAttribute("multiple") === "false";
        $this->maxSize = $node->getAttribute("max_size") ?: null;
        $this->type = $node->getAttribute("type") ?: null;
        $this->inputType = $node->getAttribute("input_type") ?: null;
    }
    public function getAccept() : ?string
    {
        return $this->accept;
    }
    public function isMultiple() : bool
    {
        return $this->multiple;
    }
    public function getMaxSize() : ?int
    {
        return $this->maxSize;
    }
    public function getType() : ?string
    {
        return $this->type;
    }
    public function getInputType() : ?string
    {
        return $this->inputType;
    }
    public function omitJson(mixed $data): ?array
    {
        return [
            'accept' => $this->getAccept(),
            'multiple' => $this->isMultiple(),
            'max_size' => $this->getMaxSize(),
            'type' => $this->getType(),
            'input_type' => $this->getInputType()
        ];
    }
}