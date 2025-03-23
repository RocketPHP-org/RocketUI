<?php

namespace RocketPhp\RocketUI\Views\Form\Field\Abstract;

use RocketPhp\RocketRule\Condition\ConditionBuilder;
use RocketPhp\RocketUI\Views\Form\AbstractCommonAttributes;

abstract class AbstractField extends AbstractCommonAttributes
{
    public function getJson(mixed $data): ?array
    {
        if (($this->evaluateCondition($data, $this->isHidden()) && $this->isHidden()) ||
            (!$this->evaluateCondition($data, $this->isVisible()) && $this->isVisible())
        ) {
            return null;
        }

        if ($data && is_object($data)) {
            $fieldName = $this->getName();
            if (isset($data->$fieldName)) {
                $this->value = $data->$fieldName;
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
            'type' => (new \ReflectionClass($this))->getShortName(),
        ];

        if (method_exists($this, 'omitJson')) {
            $additional = $this->omitJson($data);
            if (is_array($additional)) {
                $parentJson = array_merge($parentJson, $additional);
            }
        }

        return array_filter($parentJson, fn($value) => ($value !== null) && ($value !== ''));
    }

    public function evaluateCondition(mixed $data, ?string $dsl): bool
    {
        if (!empty($dsl)) {

            if ($dsl === 'true') {
                return true;
            }
            if ($dsl === 'false') {
                return false;
            }
            if ($dsl === 'is_new()') {
                return empty($data);
            }
            if ($dsl === '!is_new()') {
                return !empty($data);
            }

            if (is_object($data) && preg_match('/^(\!?)([a-zA-Z_][a-zA-Z0-9_]*)\(\)$/', $dsl, $matches)) {
                $negate = $matches[1] === '!';
                $method = $matches[2];

                if (method_exists($data, $method)) {
                    $result = $data->$method();
                    return $negate ? !$result : $result;
                }
            }

            $condition = ConditionBuilder::build($dsl);
            return $condition->isSatisfiedBy($data);
        }

        return false;
    }

}
