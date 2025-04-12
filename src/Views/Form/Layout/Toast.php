<?php

namespace RocketPhp\RocketUI\Views\Form\Layout;

use RocketPhp\RocketUI\Views\Form\Layout\Abstract\AbstractLayout;

class Toast extends AbstractLayout
{
    private string $type;
    private string $description;

    public function __construct(\DOMElement $toastNode)
    {
        parent::__construct($toastNode);
        $this->type = $toastNode->getAttribute("type") ?? 'default';
        $this->description = $toastNode->getAttribute("description") ?? '';
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function omitJson(mixed $data): array
    {
        $jsonResponse = [
            'type' => $this->getType(),
            'description' => $this->getDescription(),
        ];

        return $jsonResponse;
    }

}