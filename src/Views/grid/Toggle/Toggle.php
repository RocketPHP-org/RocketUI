<?php

namespace RocketPhp\RocketUI\Views\Grid\Toggle;

class Toggle
{
    private string $name;
    private ?string $label = null;
    private ?string $icon = null;
    private ?string $icon_position = null;
    private ?string $type = null;
    private ?string $condition = null;
    private array $params = [];

    public function __construct(\DOMElement $node)
    {
        $this->name = $node->getAttribute('name');
        $this->label = $node->getAttribute('label') ?: null;
        $this->type = $node->getAttribute('type') ?: null;
        $this->icon = $node->getAttribute('icon') ?: null;
        $this->icon_position = $node->getAttribute('icon_position') ?: null;
        $this->condition = $node->getAttribute('condition') ?: null;

        foreach ($node->getElementsByTagName('param') as $paramNode) {
            if ($paramNode->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $name = $paramNode->getAttribute('name');
            $value = $paramNode->getAttribute('value');

            if ($name && $value) {
                $this->params[$name] = $value;
            }
        }
    }

    public function getJson(): array
    {
        return array_filter([
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type,
            'icon' => $this->icon,
            'icon_position' => $this->icon_position,
            'condition' => $this->condition,
            'params' => $this->params,
        ], fn($value) => $value !== null && $value !== []);
    }
}