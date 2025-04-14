<?php

namespace RocketPhp\RocketUI\Views\Form\Layout;

use RocketPhp\RocketUI\Views\Form\Field\File;
use RocketPhp\RocketUI\Views\Form\Field\Header;
use RocketPhp\RocketUI\Views\Form\Field\Input;
use RocketPhp\RocketUI\Views\Form\Field\Paragraph;
use RocketPhp\RocketUI\Views\Form\Field\Select;
use RocketPhp\RocketUI\Views\Form\Field\Textarea;
use RocketPhp\RocketUI\Views\Form\Field\UseCase;
use RocketPhp\RocketUI\Views\Form\Field\Status;
use RocketPhp\RocketUI\Views\Form\Field\Thumb;
use RocketPhp\RocketUI\Views\Form\Layout\Tabs;
use RocketPhp\RocketUI\Views\Form\Layout\Abstract\AbstractLayout;

class Container extends AbstractLayout
{
    private ?string $type;
    private ?int $index;
    private ?string $validIcon;
    private ?string $invalidIcon;
    private ?string $inProgressIcon;
    private ?string $direction;
    private ?bool $collapsed;
    private ?bool $collapsible;
    private array $fields = [];

    public function __construct(\DOMElement $containerNode)
    {
        parent::__construct($containerNode);
        $this->id = $containerNode->getAttribute("id") ?? '';
        $this->label = $containerNode->getAttribute("label") ?? '';
        $this->type = $containerNode->getAttribute("type") ?: null;
        $this->index = $containerNode->hasAttribute("index") ? (int) $containerNode->getAttribute("index") : null;
        $this->validIcon = $containerNode->getAttribute("valid_icon") ?: null;
        $this->invalidIcon = $containerNode->getAttribute("invalid_icon") ?: null;
        $this->inProgressIcon = $containerNode->getAttribute("inProgress_icon") ?: null;
        $this->direction = $containerNode->getAttribute("direction") ?: null;
        $this->collapsed = $containerNode->hasAttribute("collapsed") ? $containerNode->getAttribute("collapsed") == "true" : null;
        $this->collapsible = $containerNode->hasAttribute("collapsible") ? $containerNode->getAttribute("collapsible") == "true" : null;

        foreach ($containerNode->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            switch ($child->nodeName) {
                case "input":
                    $this->fields[] = new Input($child);
                    break;
                case "select":
                    $this->fields[] = new Select($child);
                    break;
                case "file":
                    $this->fields[] = new File($child);
                    break;
                case "textarea":
                    $this->fields[] = new Textarea($child);
                    break;
                case "triggerUseCase":
                    $this->fields[] = new UseCase($child);
                    break;
                case "container":
                    $this->fields[] = new Container($child);
                    break;
                case "header":
                    $this->fields[] = new Header($child);
                    break;
                case "p":
                    $this->fields[] = new Paragraph($child);
                    break;
                case "status":
                    $this->fields[] = new Status($child);
                    break;
                case "thumb":
                    $this->fields[] = new Thumb($child);
                    break;
                case "tabs":
                    $this->fields[] = new Tabs($child);
                    break;
                case "toast":
                    $this->fields[] = new Toast($child);
                    break;
                default:
                    // Optionnel : log ou ignorer proprement
                    break;
            }
        }
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getIndex(): ?int
    {
        return $this->index;
    }

    public function getValidIcon(): ?string
    {
        return $this->validIcon;
    }

    public function getInvalidIcon(): ?string
    {
        return $this->invalidIcon;
    }

    public function getInProgressIcon(): ?string
    {
        return $this->inProgressIcon;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }
    public function isCollapsed(): ?bool
    {
        return $this->collapsed;
    }
    public function isCollapsible(): ?bool
    {
        return $this->collapsible;
    }

    public function omitJson(mixed $data): array
    {
        $jsonResponse = [
            'type' => $this->getType() ?: (new \ReflectionClass($this))->getShortName(),
            'direction' => $this->getDirection(),
            'label' => $this->getLabel(),
            'validIcon' => $this->getValidIcon(),
            'invalidIcon' => $this->getInvalidIcon(),
            'inProgressIcon' => $this->getInProgressIcon(),
            'collapsed' => $this->isCollapsed(),
            'collapsible' => $this->isCollapsible(),
            'elements' => [],
        ];

        foreach ($this->fields as $field) {
            $fieldResponse = $field->getJson($data);
            if ($fieldResponse) {
                $jsonResponse['elements'][] = $fieldResponse;
            }
        }

        return $jsonResponse;
    }
}
