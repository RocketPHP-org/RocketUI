<?php

namespace RocketPhp\RocketUI\Views\Form\Layout;


use RocketPhp\RocketUI\Views\Form\Layout\Abstract\AbstractLayout;

class Layout extends AbstractLayout
{
    private string $type;
    private array $containers = [];
    private ?Tabs $tabs = null;
    private ?Toast $toast = null;

    public function __construct(\DOMElement $layoutNode)
    {
        $this->type = $layoutNode->getAttribute("type") ?? 'default';

        foreach ($layoutNode->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            switch ($child->nodeName) {
                case "container":
                    $this->containers[] = new Container($child);
                    break;
                case "tabs":
                    $this->tabs = new Tabs($child);
                    break;
                case "toast":
                    $this->toast = new Toast($child);
                    break;
            }
        }
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getContainers(): array
    {
        return $this->containers;
    }

    public function getTabs(): ?Tabs
    {
        return $this->tabs;
    }

    public function getToast(): ?Toast
    {
        return $this->toast;
    }

    public function getJson(mixed $data) : array
    {
        $jsonResponse = [];

        foreach ($this->containers as $container) {
            $jsonResponse[] = $container->getJson($data);
        }

        return $jsonResponse;
    }
}
