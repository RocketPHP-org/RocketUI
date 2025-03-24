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
            $fieldName = $this->getName();
            if (is_string($fieldName) && $fieldName !== '') {
                $value = $this->getNestedValue($data, $fieldName);
                $this->value = is_scalar($value) ? (string)$value : null;
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
            'component' => (new \ReflectionClass($this))->getShortName(),
        ];

        //'transitions' => $data::$FLOW['transitions']['status']
        if ($data::$FLOW['transitions'][$this->getName()]) {
            $parentJson['transitions'] = $data::$FLOW['transitions'][$this->getName()];
        }


        if (method_exists($this, 'omitJson')) {
            $additional = $this->omitJson($data);
            if (is_array($additional)) {
                $parentJson = array_merge($parentJson, $additional);
            }
        }

        return array_filter($parentJson, fn($value) => ($value !== null) && ($value !== ''));
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
