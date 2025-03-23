<?php

namespace RocketPhp\RocketUI\Views\Form;

abstract class AbstractCommonAttributes
{
    protected ?string $id;
    protected ?string $name;
    protected ?string $label;
    protected ?string $labelPosition;
    protected ?string $icon;
    protected ?string $iconPosition;
    protected ?string $required;
    protected ?string $placeholder;
    protected ?string $width;
    protected ?string $readonly;
    protected ?string $hide;
    protected ?string $visible;
    protected ?string $rightLabel;
    protected ?string $helperLabel;
    protected ?string $helper;
    protected ?string $error;
    protected ?string $tooltip;
    protected ?string $value;
    protected ?string $hideIf;
    protected ?string $showIf;
    protected ?string $disableIf;
    protected ?string $enableIf;
    protected ?string $invalidIf;
    protected ?string $condition;
    protected ?string $onChange;
    protected ?string $default;
    private ?string $csCondition;

    public function __construct(\DOMElement $node)
    {
        $this->id = $node->getAttribute("id") ?: null;
        $this->name = $node->getAttribute("name") ?: null;
        $this->label = $node->getAttribute("label") ?: null;
        $this->labelPosition = $node->getAttribute("label_position") ?: null;
        $this->icon = $node->getAttribute("icon") ?: null;
        $this->iconPosition = $node->getAttribute("icon_position") ?: null;
        $this->required = $node->getAttribute("required") ?: null;
        $this->placeholder = $node->getAttribute("placeholder") ?: null;
        $this->width = $node->getAttribute("width") ?: null;
        $this->readonly = $node->getAttribute("readonly") ?: null;
        $this->hide = $node->getAttribute("hide") ?: null;
        $this->visible = $node->getAttribute("visible") ?: null;
        $this->rightLabel = $node->getAttribute("right_label") ?: null;
        $this->helperLabel = $node->getAttribute("helper_label") ?: null;
        $this->helper = $node->getAttribute("helper") ?: null;
        $this->error = $node->getAttribute("error") ?: null;
        $this->tooltip = $node->getAttribute("tooltip") ?: null;
        $this->value = $node->getAttribute("value") ?: null;
        $this->hideIf = $node->getAttribute("hideIf") ?: null;
        $this->showIf = $node->getAttribute("showIf") ?: null;
        $this->disableIf = $node->getAttribute("disableIf") ?: null;
        $this->enableIf = $node->getAttribute("enableIf") ?: null;
        $this->invalidIf = $node->getAttribute("invalidIf") ?: null;
        $this->condition = $node->getAttribute("condition") ?: null;
        $this->onChange = $node->getAttribute("on_change") ?: null;
        $this->default = $node->getAttribute("default") ?: null;
        $this->csCondition = $node->getAttribute("csCondition") ?: null;
    }

    public function getId(): ?string { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function getLabel(): ?string { return $this->label; }
    public function getLabelPosition(): ?string { return $this->labelPosition; }
    public function getIcon(): ?string { return $this->icon; }
    public function getIconPosition(): ?string { return $this->iconPosition; }
    public function isRequired(): ?string { return $this->required; }
    public function getPlaceholder(): ?string { return $this->placeholder; }
    public function getWidth(): ?string { return $this->width; }
    public function isReadonly(): ?string { return $this->readonly; }
    public function isHidden(): ?string { return $this->hide; }
    public function isVisible(): ?string { return $this->visible; }
    public function getRightLabel(): ?string { return $this->rightLabel; }
    public function getHelperLabel(): ?string { return $this->helperLabel; }
    public function getHelper(): ?string { return $this->helper; }
    public function getError(): ?string { return $this->error; }
    public function getTooltip(): ?string { return $this->tooltip; }
    public function getValue(): ?string { return $this->value; }
    public function getHideIf(): ?string { return $this->hideIf; }
    public function getShowIf(): ?string { return $this->showIf; }
    public function getDisableIf(): ?string { return $this->disableIf; }
    public function getEnableIf(): ?string { return $this->enableIf; }
    public function getInvalidIf(): ?string { return $this->invalidIf; }
    public function getCondition(): ?string { return $this->condition; }
    public function getOnChange(): ?string { return $this->onChange; }
    public function getDefault(): ?string { return $this->default; }
    public function getCsCondition(): ?string { return $this->csCondition; }
}