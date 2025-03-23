<?php

namespace RocketPhp\RocketUI\Views\Form\Layout;

use RocketPhp\RocketUI\Views\Form\Layout\Abstract\AbstractLayout;

class Tabs extends AbstractLayout
{
    private string $id;
    private string $label;
    private ?string $defaultTab;
    private array $containers = [];

    public function __construct(\DOMElement $tabsNode)
    {
        $this->id = $tabsNode->getAttribute("id") ?? '';
        $this->label = $tabsNode->getAttribute("label") ?? '';
        $this->defaultTab = $tabsNode->getAttribute("defaultTab") ?? null;

        foreach ($tabsNode->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            if ($child->nodeName === "container") {
                $this->containers[] = new Container($child);
            }
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDefaultTab(): ?string
    {
        return $this->defaultTab;
    }

    public function getContainers(): array
    {
        return $this->containers;
    }

}