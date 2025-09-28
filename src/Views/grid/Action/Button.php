<?php

namespace RocketPhp\RocketUI\Views\Grid\Action;

class Button
{
    private ?string $id;
    private ?string $name;
    private ?string $label;
    private ?string $tooltip;
    private ?string $condition;
    private ?string $confirm;
    private string $type;
    private ?string $redirection;
    private array $params = [];

    public function __construct(\DOMElement $node)
    {
        $this->id = $node->getAttribute('id') ?: null;
        $this->name = $node->getAttribute('name') ?: null;
        $this->label = $node->getAttribute('label') ?: null;
        $this->tooltip = $node->getAttribute('tooltip') ?: null;
        $this->condition = $node->getAttribute('condition') ?: null;
        $this->confirm = $node->getAttribute('confirm') ?: null;
        $this->redirection = $node->getAttribute('redirection') ?: null;
        $this->type = $node->getAttribute('type');

        foreach ($node->getElementsByTagName('param') as $paramNode) {
            if ($paramNode->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $paramName = $paramNode->getAttribute('name');
            $paramValue = $paramNode->getAttribute('value');

            if ($paramName !== '' && $paramValue !== '') {
                $this->params[$paramName] = $paramValue;
            }
        }
    }

    public function getJson(mixed $data = null): array
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->label,
            'tooltip' => $this->tooltip,
            'condition' => $this->condition,
            'confirm' => $this->confirm,
            'redirection' => $this->redirection,
            'type' => $this->type,
            'params' => $this->params ?: null,
        ], fn($value) => $value !== null);
    }
}
