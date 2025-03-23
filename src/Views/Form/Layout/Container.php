<?php

namespace RocketPhp\RocketUI\Views\Form\Layout;

use RocketPhp\RocketUI\Views\Form\Field\File;
use RocketPhp\RocketUI\Views\Form\Field\Header;
use RocketPhp\RocketUI\Views\Form\Field\Input;
use RocketPhp\RocketUI\Views\Form\Field\Paragraph;
use RocketPhp\RocketUI\Views\Form\Field\Select;
use RocketPhp\RocketUI\Views\Form\Field\Textarea;
use RocketPhp\RocketUI\Views\Form\Field\UseCase;
use RocketPhp\RocketUI\Views\Form\Layout\Abstract\AbstractLayout;

class Container extends AbstractLayout
{
    private string $id;
    private string $label;
    private ?string $type;
    private array $fields = [];
    public function __construct(\DOMElement $containerNode)
    {
        $this->id = $containerNode->getAttribute("id") ?? '';
        $this->label = $containerNode->getAttribute("label") ?? '';
        $this->type = $containerNode->getAttribute("type") ?? null;

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
                    $this->fields[] = new TextArea($child);
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getJson(mixed $data) : array
    {
        $jsonResponse = [
            'id' => $this->id,
            'label' => $this->label,
            'type' => (new \ReflectionClass($this))->getShortName(),
            'fields' => [],
        ];

        foreach ($this->fields as $field) {
            $fieldResponse = $field->getJson($data);
            if ($fieldResponse) {
                $jsonResponse['fields'][] = $fieldResponse;
            }
        }

        return $jsonResponse;
    }
}