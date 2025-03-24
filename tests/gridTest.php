<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use RocketPhp\RocketUI\UIEngine;
use RocketPhp\RocketUI\Views\Form\Form;
use stdClass;


class GridTest extends TestCase
{
    public function testFormXSDValidation()
    {
        $xml = new \DOMDocument();
        $xml->load(__DIR__ . '/data/grid.xml');

        libxml_use_internal_errors(true);

        if (!$xml->schemaValidate(__DIR__ . '/../src/Views/Grid/XSD/schema.xsd')) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                echo "[XSD Error] " . trim($error->message) . "\n";
            }
            libxml_clear_errors();
        }

        $this->assertTrue(
            $xml->schemaValidate(__DIR__ . '/../src/Views/Grid/XSD/schema.xsd'),
            "Le fichier XML ne respecte pas le schéma XSD."
        );
    }

}



