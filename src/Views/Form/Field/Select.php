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
    }

    public function getBadge(): ?string
    {
        return $this->badge;
    }

    public function getChip(): ?string
    {
        return $this->chip;
    }

    public function getChipPosition(): ?string
    {
        return $this->chipPosition;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getHelpChoice(): ?string
    {
        return $this->helpChoice;
    }

    public function getInlineCardThumbnails(): ?string
    {
        return $this->inlineCardThumbnails;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

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
        ];
    }
}
