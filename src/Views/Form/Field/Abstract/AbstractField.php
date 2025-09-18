<?php

namespace RocketPhp\RocketUI\Views\Form\Field\Abstract;

use RocketPhp\RocketRule\Condition\ConditionBuilder;
use RocketPhp\RocketUI\Views\Form\AbstractCommonAttributes;
use RocketPhp\RocketUI\Views\Service\ConditionEvaluation;
use RocketPhp\RocketUI\Views\Service\ValueParser;

abstract class AbstractField extends AbstractCommonAttributes
{
    public function getJson(mixed $data): ?array
    {
        if (
            ($this->evaluateCondition($data, $this->isHidden()) && $this->isHidden()) ||
            (!$this->evaluateCondition($data, $this->isVisible()) && $this->isVisible())
        ) {
            return null;
        }

        if ($data && is_object($data)) {
            $dataPath = $this->resolveDataPath();
            if ($dataPath !== null) {
                $value = $this->getNestedValue($data, $dataPath);
                $normalized = $this->normalizeFieldValue($value);
                if ($normalized !== null) {
                    $this->value = $normalized;
                }
            }
        }

        $parentJson = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'labelPosition' => $this->getLabelPosition(),
            'icon' => $this->getIcon(),
            'iconPosition' => $this->getIconPosition(),
            'required' => $this->evaluateCondition($data, $this->isRequired()),
            'placeholder' => $this->getPlaceholder(),
            'width' => $this->getWidth(),
            'readonly' => $this->evaluateCondition($data, $this->isReadonly()),
            'rightLabel' => $this->getRightLabel(),
            'helperLabel' => $this->getHelperLabel(),
            'helper' => $this->getHelper(),
            'error' => $this->getError(),
            'tooltip' => $this->getTooltip(),
            'value' => $this->getValue(),
            'hideIf' => $this->getHideIf(),
            'showIf' => $this->getShowIf(),
            'disableIf' => $this->getDisableIf(),
            'enableIf' => $this->getEnableIf(),
            'invalidIf' => $this->getInvalidIf(),
            'default' => $this->getDefault(),
            'onChange' => $this->getOnChange(),
            'condition' => $this->getCondition(),
            'csCondition' => $this->getCsCondition(),
            'component' => method_exists($this, 'getType') && $this->getType()
                ? lcfirst((new \ReflectionClass($this))->getShortName()) . ucfirst($this->getType())
                : lcfirst((new \ReflectionClass($this))->getShortName()),
        ];

        //'transitions' => $data::$FLOW['transitions']['status']
        $name = $this->getName();

        if (!empty($name) && isset($data::$FLOW['transitions'][$name])) {
            $parentJson['transitions'] = $data::$FLOW['transitions'][$name];
        }


        if (method_exists($this, 'omitJson')) {
            $additional = $this->omitJson($data);
            if (is_array($additional)) {
                $parentJson = array_merge($parentJson, $additional);
            }
        }

        return array_filter($parentJson, fn($value) => ($value !== null) && ($value !== ''));
    }

    private function normalizeFieldValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $this->formatDateTimeValue($value);
        }

        if (is_scalar($value) || $value instanceof \Stringable) {
            return (string)$value;
        }

        return null;
    }

    private function formatDateTimeValue(\DateTimeInterface $value): string
    {
        $type = method_exists($this, 'getType') ? $this->getType() : null;
        $format = method_exists($this, 'getFormat') ? $this->getFormat() : null;

        $phpFormat = $this->convertFormatToPhp($format, $type);

        if (!$phpFormat) {
            return match ($type) {
                'time' => $value->format('H:i:s'),
                'datetime' => $value->format('c'),
                default => $value->format('Y-m-d'),
            };
        }

        return $value->format($phpFormat);
    }

    private function convertFormatToPhp(?string $format, ?string $type): ?string
    {
        if (!$format) {
            return null;
        }

        $map = [
            'yyyy' => 'Y',
            'yy' => 'y',
            'dd' => 'd',
            'HH' => 'H',
            'hh' => 'h',
            'ss' => 's',
        ];

        $translated = strtr($format, $map);
        $translated = str_replace('MM', 'mm', $translated);

        if (!str_contains($translated, 'mm')) {
            return $translated;
        }

        $segments = explode('mm', $translated);
        $result = array_shift($segments);

        foreach ($segments as $index => $segment) {
            $replacement = match ($type) {
                'time' => 'i',
                'datetime' => $index === 0 ? 'm' : 'i',
                default => 'm',
            };

            $result .= $replacement . $segment;
        }

        return $result;
    }

    private function resolveDataPath(): ?string
    {
        $fieldName = $this->getName();
        if (is_string($fieldName) && $fieldName !== '') {
            return $fieldName;
        }

        $fieldId = $this->getId();
        if (is_string($fieldId) && $fieldId !== '') {
            return $fieldId;
        }

        return null;
    }

    private function getNestedValue(object $data, ?string $path): mixed
    {
        return ValueParser::NestedValue($data, $path);
    }

    public function evaluateCondition(mixed $data, ?string $dsl): bool
    {
        return ConditionEvaluation::evaluateCondition($data, $dsl);
    }
}
