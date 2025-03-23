<?php

namespace RocketPhp\RocketUI\Views\Form\Layout;

use RocketPhp\RocketUI\Views\Form\Layout\Abstract\AbstractLayout;

class Tabs extends AbstractLayout
{
    private ?string $defaultTab;
    private array $containers = [];

    public function __construct(\DOMElement $tabsNode)
    {
        parent::__construct($tabsNode);
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

    public function getDefaultTab(): ?string
    {
        return $this->defaultTab;
    }

    public function getContainers(): array
    {
        return $this->containers;
    }
    public function omitJson(mixed $data): array
    {
        $jsonResponse = [
            'type' => (new \ReflectionClass($this))->getShortName(),
            'elements' => [],
        ];

        if ($this->getDefaultTab()) {
            $jsonResponse['defaultTab'] = $this->getDefaultTab();
        }

        foreach ($this->containers as $container) {
            $jsonResponse['elements'][] = $container->omitJson($data);
        }

        return $jsonResponse;
    }

}