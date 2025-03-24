<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use RocketPhp\RocketUI\UIEngine;
use RocketPhp\RocketUI\Views\Form\Form;
use RocketPhp\RocketUI\Views\Grid\Grid;
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

    public function testBuildUI()
    {
        $form = new Grid(__DIR__ . '/data/grid.xml');

        $data = new FormTestInstance();

        $json = new UIEngine();
        $json = $json->buildGrid($form, $data);

        $decoded = json_decode($json, true);
        $prettyJson = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        file_put_contents(__DIR__ . '/data/BuiltGrid.json', $prettyJson);
    }

}



