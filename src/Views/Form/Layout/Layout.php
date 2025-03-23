<?php

namespace RocketPhp\RocketUI\Views\Form\Layout;


use RocketPhp\RocketUI\Views\Form\Layout\Abstract\AbstractLayout;

class Layout extends AbstractLayout
{
    private string $type;
    private array $elements = [];

    public function __construct(\DOMElement $layoutNode)
    {
        $this->type = $layoutNode->getAttribute("type") ?? 'default';

        foreach ($layoutNode->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            switch ($child->nodeName) {
                case "container":
                    $this->elements[] = new Container($child);
                    break;
                case "tabs":
                    $this->elements[] = new Tabs($child);
                    break;
                case "toast":
                    $this->elements[] = new Toast($child);
                    break;
            }
        }
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    public function getJson(mixed $data) : array
    {
        $jsonResponse = [];

        foreach ($this->elements as $container) {
            $jsonResponse[] = $container->getJson($data);
        }

        return $jsonResponse;
    }
}
