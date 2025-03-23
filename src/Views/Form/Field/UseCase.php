<?php

namespace RocketPhp\RocketUI\Views\Form\Field;

use RocketPhp\RocketUI\Views\Form\Field\Abstract\AbstractField;

class UseCase extends AbstractField
{
    private ?string $method;
    private ?string $module;
    private ?string $model;
    private ?string $recordId;

    /** @var array<string, string> */
    private array $params = [];

    public function __construct(\DOMElement $node)
    {
        parent::__construct($node);
        $this->method = $node->getAttribute('method');
        $this->module = $node->getAttribute('module');
        $this->model = $node->getAttribute('model');
        $this->recordId = $node->getAttribute('recordId');

        foreach ($node->getElementsByTagName('param') as $paramNode) {
            if ($paramNode instanceof \DOMElement) {
                $name = $paramNode->getAttribute('name');
                $value = $paramNode->getAttribute('value');
                if ($name !== '') {
                    $this->params[$name] = $value;
                }
            }
        }
    }

    public function getMethod(): ?string { return $this->method; }
    public function getModule(): ?string { return $this->module; }
    public function getModel(): ?string { return $this->model; }
    public function getRecordId(): ?string { return $this->recordId; }

    public function getParams(): array
    {
        return $this->params;
    }

    public function omitJson(mixed $data): ?array
    {
        return [
            'method' => $this->getMethod(),
            'module' => $this->getModule(),
            'model' => $this->getModel(),
            'recordId' => $this->getRecordId(),
            'params' => $this->getParams()
        ];
    }
}
