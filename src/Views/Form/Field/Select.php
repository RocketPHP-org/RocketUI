<?php

namespace RocketPhp\RocketUI\Views\Form\Field;

use RocketPhp\RocketUI\Views\Form\Field\Abstract\AbstractField;

class Select extends AbstractField
{
    private ?string $badge;
    private ?string $chip;
    private ?string $chipPosition;
    private ?string $description;
    private ?string $helpChoice;
    private ?string $inlineCardThumbnails;
    private ?string $type;

    private ?string $remote;
    private ?string $valueField;
    private ?string $labelField;

    private bool $multi = false;

    /** @var array<array{label: string, value: string}> */
    private array $options = [];

    /** @var array<string, string> */
    private array $filters = [];

    public function __construct(\DOMElement $node)
    {
        parent::__construct($node);
        $this->badge = $node->getAttribute('badge');
        $this->chip = $node->getAttribute('chip');
        $this->chipPosition = $node->getAttribute('chip_position');
        $this->description = $node->getAttribute('description');
        $this->helpChoice = $node->getAttribute('help-choice');
        $this->inlineCardThumbnails = $node->getAttribute('inline_card_thumbnails');
        $this->type = $node->getAttribute('type');

        $this->remote = $node->getAttribute('remote') ?: null;
        $this->valueField = $node->getAttribute('valueField') ?: null;
        $this->labelField = $node->getAttribute('labelField') ?: null;
        $this->multi = $node->getAttribute('multi') === 'true';

        foreach ($node->childNodes as $child) {
            if ($child instanceof \DOMElement) {
                if ($child->nodeName === 'option') {
                    $label = $child->getAttribute('label');
                    $value = $child->getAttribute('value');
                    if ($label !== '' && $value !== '') {
                        $this->options[] = ['label' => $label, 'value' => $value];
                    }
                } elseif ($child->nodeName === 'filter') {
                    $field = $child->getAttribute('field');
                    $value = $child->getAttribute('value');
                    if ($field !== '' && $value !== '') {
                        $this->filters[$field] = $value;
                    }
                }
            }
        }
    }

    public function getBadge(): ?string { return $this->badge; }
    public function getChip(): ?string { return $this->chip; }
    public function getChipPosition(): ?string { return $this->chipPosition; }
    public function getDescription(): ?string { return $this->description; }
    public function getHelpChoice(): ?string { return $this->helpChoice; }
    public function getInlineCardThumbnails(): ?string { return $this->inlineCardThumbnails; }
    public function getType(): ?string { return $this->type; }

    public function getRemote(): ?string { return $this->remote; }
    public function getValueField(): ?string { return $this->valueField; }
    public function getLabelField(): ?string { return $this->labelField; }
    public function isMulti(): bool { return $this->multi; }
    public function getOptions(): array { return $this->options; }
    public function getFilters(): array { return $this->filters; }

    public function omitJson(mixed $data): ?array
    {
        return [
            'badge' => $this->getBadge(),
            'chip' => $this->getChip(),
            'chip_position' => $this->getChipPosition(),
            'description' => $this->getDescription(),
            'help_choice' => $this->getHelpChoice(),
            'inline_card_thumbnails' => $this->getInlineCardThumbnails(),
            'type' => $this->getType(),
            'remote' => $this->getRemote(),
            'valueField' => $this->getValueField(),
            'labelField' => $this->getLabelField(),
            'multi' => $this->isMulti(),
            'options' => $this->getOptions() ?: null,
            'filters' => $this->getFilters() ?: null,
        ];
    }
}
