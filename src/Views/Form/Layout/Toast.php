<?php

namespace RocketPhp\RocketUI\Views\Form\Layout;

use RocketPhp\RocketUI\Views\Form\Layout\Abstract\AbstractLayout;

class Toast extends AbstractLayout
{
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