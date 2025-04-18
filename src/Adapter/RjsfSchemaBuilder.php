<?php

namespace RocketPhp\RocketUI\Adapter;

class RjsfSchemaBuilder
{
    private array $schema = [
        'type' => 'object',
        'title' => '',
        'description' => '',
        'properties' => [],
        'required' => []
    ];

    private array $uiSchema = [];
    private array $formData = [];

    public function convert(array $json): array
    {
        $this->schema['title'] = $json['metadata']['title'] ?? '';
        $this->schema['description'] = $json['metadata']['description'] ?? '';

        foreach ($json['layout'] as $block) {
            $blockKey = $block['id'] ?? $block['label'] ?? uniqid('section_');
            $this->parseLayoutBlock($block['elements'] ?? [], $blockKey, $block);
        }

        if (!empty($json['actions'])) {
            $this->uiSchema['ui:submitButtonOptions'] = [
                'norender' => false,
                'submitText' => 'Submit'
            ];
            foreach ($json['actions'] as $action) {
                if ($action['type'] === 'reset') {
                    $this->uiSchema['ui:buttons'][] = ['type' => 'reset', 'label' => $action['label'] ?? 'Reset'];
                }
                if ($action['type'] === 'button') {
                    $this->uiSchema['ui:buttons'][] = ['type' => 'button', 'label' => $action['label'] ?? 'Cancel'];
                }
                if ($action['type'] === 'primary') {
                    $this->uiSchema['ui:submitButtonOptions']['submitText'] = $action['label'] ?? 'Submit';
                }
            }
        }

        return [
            'schema' => $this->schema,
            'uiSchema' => $this->uiSchema,
            'formData' => $this->formData
        ];
    }

    private function parseLayoutBlock(array $elements, string $blockKey, array $blockMeta = []): void
    {
        $this->uiSchema[$blockKey] = ['ui:layout' => []];

        $this->uiSchema[$blockKey]['options'] = [
            'label' => $blockMeta['label'] ?? null,
            'collapsible' => $blockMeta['collapsible'] ?? false,
            'collapsed' => $blockMeta['collapsed'] ?? false,
            'direction' => $blockMeta['direction'] ?? 'row',
            'condition' => $blockMeta['condition'] ?? null,
            'validIcon' => $blockMeta['validIcon'] ?? null,
            'invalidIcon' => $blockMeta['invalidIcon'] ?? null,
            'inProgressIcon' => $blockMeta['inProgressIcon'] ?? null
        ];

        foreach ($elements as $element) {
            if (isset($element['elements']) && $element['component'] === 'Tabs') {
                $tabKey = $element['id'] ?? $element['label'] ?? uniqid('tabs_');
                $this->uiSchema[$tabKey] = ['ui:field' => 'Tabs', 'tabs' => []];

                foreach ($element['elements'] as $tab) {
                    $tabLabel = $tab['label'] ?? 'Tab';
                    $fields = [];
                    foreach ($tab['elements'] ?? [] as $subElement) {
                        $fieldName = $subElement['name'] ?? $subElement['id'] ?? null;
                        if ($fieldName) {
                            $this->createSchemaProperty($fieldName, $subElement, strtolower($subElement['component'] ?? ''));
                            $fields[] = $fieldName;
                        }
                    }
                    $this->uiSchema[$tabKey]['tabs'][] = [
                        'name' => $tabLabel,
                        'fields' => $fields
                    ];
                }
                $this->uiSchema[$blockKey]['ui:layout'][] = ['ui:field' => 'Tabs', 'tabs' => $this->uiSchema[$tabKey]['tabs']];
                continue;
            }

            if (isset($element['elements'])) {
                $subKey = $element['id'] ?? $element['label'] ?? uniqid('section_');
                $this->parseLayoutBlock($element['elements'], $subKey, $element);
                continue;
            }

            $component = strtolower($element['component'] ?? '');
            $name = $element['name'] ?? $element['id'] ?? null;

            if (in_array($component, ['header', 'toast_info', 'paragraph', 'thumb'])) {
                $this->uiSchema[$blockKey]['ui:layout'][] = [
                    'ui:field' => $this->mapSpecialField($component),
                    'options' => [
                        'label' => $element['label'] ?? null,
                        'description' => $element['description'] ?? $element['text'] ?? null,
                        'type' => $element['type'] ?? null
                    ]
                ];
                continue;
            }

            if ($component === 'usecase') {
                $this->uiSchema[$blockKey]['ui:layout'][] = [
                    'ui:field' => 'UseCase',
                    'name' => $name,
                    'options' => [
                        'method' => $element['method'] ?? '',
                        'params' => $element['params'] ?? []
                    ]
                ];
                continue;
            }

            if (!$name || !$component) {
                continue;
            }

            $this->createSchemaProperty($name, $element, $component);

            $this->uiSchema[$blockKey]['ui:layout'][] = [
                $name => ['md' => $this->mapWidth($element['width'] ?? null)]
            ];
        }
    }

    private function createSchemaProperty(string $name, array $element, string $component): void
    {
        $type = $element['type'] ?? 'string';
        $label = $element['label'] ?? ucfirst($name);

        $property = [
            'type' => $this->mapType($type),
            'title' => $label
        ];

        if (!empty($element['multi'])) {
            $property['type'] = 'array';
            $property['items'] = ['type' => 'string'];
            $this->uiSchema[$name]['ui:widget'] = 'checkboxes';
        }

        if (isset($element['format'])) {
            $property['format'] = $element['format'];
        }

        if (isset($element['options']) && is_array($element['options'])) {
            $property['enum'] = array_column($element['options'], 'value');
            $this->uiSchema[$name]['ui:enumNames'] = array_column($element['options'], 'label');
        }

        if (!empty($element['min'])) {
            $property['minimum'] = $element['min'];
        }

        if (!empty($element['max'])) {
            $property['maximum'] = $element['max'];
        }

        if (!empty($element['step'])) {
            $property['multipleOf'] = $element['step'];
        }

        $this->schema['properties'][$name] = $property;

        if (!empty($element['required'])) {
            $this->schema['required'][] = $name;
        }

        if (isset($element['value']) || isset($element['default'])) {
            $this->formData[$name] = $element['value'] ?? $element['default'];
        }

        $this->uiSchema[$name]['ui:disabled'] = $element['readonly'] ?? false;

        if (!empty($element['placeholder'])) {
            $this->uiSchema[$name]['ui:placeholder'] = $element['placeholder'];
        }

        if (!empty($element['helper']) || !empty($element['descriptionLongue'])) {
            $this->uiSchema[$name]['ui:help'] = $element['helper'] ?? $element['descriptionLongue'];
        }

        if (!empty($element['condition'])) {
            $this->uiSchema[$name]['ui:condition'] = $element['condition'];
        }

        $widget = $this->mapWidget($component);
        if ($widget) {
            $this->uiSchema[$name]['ui:widget'] = $widget;
        }
    }

    private function mapType(string $type): string
    {
        return match (strtolower($type)) {
            'text', 'email', 'chips', 'selectbox', 'url', 'password', 'phone', 'color', 'currency' => 'string',
            'date', 'time', 'datetime' => 'string',
            'number', 'range' => 'number',
            'integer' => 'integer',
            'boolean', 'checkbox' => 'boolean',
            default => 'string'
        };
    }

    private function mapWidget(string $component): ?string
    {
        return match ($component) {
            'input_email' => 'email',
            'input_text' => 'text',
            'input_password' => 'password',
            'input_date' => 'date',
            'input_time' => 'time',
            'input_datetime' => 'datetime',
            'input_phone' => 'tel',
            'input_url' => 'uri',
            'input_color' => 'color',
            'input_range' => 'range',
            'select_chips', 'select_selectbox' => 'select',
            'textarea' => 'textarea',
            'status' => 'select',
            'file' => 'file',
            default => null
        };
    }

    private function mapSpecialField(string $component): string
    {
        return match (strtolower($component)) {
            'header' => 'Header',
            'toast_info', 'toast_success', 'toast_error' => 'Toast',
            'paragraph' => 'Paragraph',
            'thumb' => 'Thumb',
            default => 'UnknownField'
        };
    }

    private function mapWidth(?string $width): int
    {
        return match ($width) {
            '1/1' => 12,
            '1/2' => 6,
            '1/3' => 4,
            '2/3' => 8,
            '1/4' => 3,
            default => 12
        };
    }
}