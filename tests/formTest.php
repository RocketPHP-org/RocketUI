<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use RocketPhp\RocketUI\UIEngine;
use RocketPhp\RocketUI\Views\Form\Form;
use stdClass;

class FormTestInstance {

    public $name = "John Doe";
    public $email = "john@doe.com";

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function isValid(): bool
    {
        return true;
    }

}


class FormTest extends TestCase
{
    public function testFormXSDValidation()
    {
        $xml = new \DOMDocument();
        $xml->load(__DIR__ . '/data/form.xml');

        libxml_use_internal_errors(true);

        if (!$xml->schemaValidate(__DIR__ . '/../src/Views/Form/XSD/schema.xsd')) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                echo "[XSD Error] " . trim($error->message) . "\n";
            }
            libxml_clear_errors();
        }

        $this->assertTrue(
            $xml->schemaValidate(__DIR__ . '/../src/Views/Form/XSD/schema.xsd'),
            "Le fichier XML ne respecte pas le schéma XSD."
        );
    }

    public function testFormLoad()
    {
        $form = new Form(__DIR__ . '/data/form.xml');
        $this->assertNotNull($form);
    }

    /**
     * @throws \Exception
     */
    public function testBuildUI()
    {
        $form = new Form(__DIR__ . '/data/form.xml');

        $data = new FormTestInstance();

        $json = new UIEngine();
        $json = $json->buildForm($form, $data);

        $decoded = json_decode($json, true);
        $prettyJson = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        file_put_contents(__DIR__ . '/data/BuiltForm.json', $prettyJson);
    }


}



