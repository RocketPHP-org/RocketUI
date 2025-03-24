<?php

namespace RocketPhp\RocketUI\Views\Grid\Column;

class Column
{
    private ?string $id;
    private ?string $type;
    private ?string $label;
    private ?string $field;
    private ?bool $sortable;
    private ?string $sortDirection;
    private ?bool $filterable;
    private ?string $filterType;
    private ?bool $resizable;
    private ?string $hidden;
    private ?string $align;
    private ?string $class;
    private ?string $template;
    private ?bool $editable;
    private ?string $editorType;
    private ?bool $computed;
    private ?string $expression;
    private ?string $visibleIf;

    public function __construct(\DOMElement $node)
    {
        $this->id = $node->getAttribute('id') ?: null;
        $this->type = $node->getAttribute('type') ?: null;
        $this->label = $node->getAttribute('label') ?: null;
        $this->field = $node->getAttribute('field') ?: null;
        $this->sortable = $node->hasAttribute('sortable') ? filter_var($node->getAttribute('sortable'), FILTER_VALIDATE_BOOLEAN) : null;
        $this->sortDirection = $node->getAttribute('sortDirection') ?: null;
        $this->filterable = $node->hasAttribute('filterable') ? filter_var($node->getAttribute('filterable'), FILTER_VALIDATE_BOOLEAN) : null;
        $this->filterType = $node->getAttribute('filterType') ?: null;
        $this->resizable = $node->hasAttribute('resizable') ? filter_var($node->getAttribute('resizable'), FILTER_VALIDATE_BOOLEAN) : null;
        $this->hidden = $node->getAttribute('hidden') ?: null;
        $this->align = $node->getAttribute('align') ?: null;
        $this->class = $node->getAttribute('class') ?: null;
        $this->template = $node->getAttribute('template') ?: null;
        $this->editable = $node->hasAttribute('editable') ? filter_var($node->getAttribute('editable'), FILTER_VALIDATE_BOOLEAN) : null;
        $this->editorType = $node->getAttribute('editorType') ?: null;
        $this->computed = $node->hasAttribute('computed') ? filter_var($node->getAttribute('computed'), FILTER_VALIDATE_BOOLEAN) : null;
        $this->expression = $node->getAttribute('expression') ?: null;
        $this->visibleIf = $node->getAttribute('visibleIf') ?: null;
    }

    public function getJson(): array
    {
        return array_filter([
            'id' => $this->id,
            'type' => $this->type,
            'label' => $this->label,
            'field' => $this->field,
            'sortable' => $this->sortable,
            'sortDirection' => $this->sortDirection,
            'filterable' => $this->filterable,
            'filterType' => $this->filterType,
            'resizable' => $this->resizable,
            'hidden' => $this->hidden,
            'align' => $this->align,
            'class' => $this->class,
            'template' => $this->template,
            'editable' => $this->editable,
            'editorType' => $this->editorType,
            'computed' => $this->computed,
            'expression' => $this->expression,
            'visibleIf' => $this->visibleIf,
        ], fn($value) => $value !== null);
    }
}
