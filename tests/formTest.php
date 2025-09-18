<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use RocketPhp\RocketUI\UIEngine;
use RocketPhp\RocketUI\Views\Form\Form;
use stdClass;


class valueObject {
    public $value = 'ValueObjectValue Net';
}

class address {
    public $id = 100;
    public $street = "123 Main St";
    public $city = "Anytown";
    public $state = "CA";
    public $zip = "12345";
}

class FormTestInstance {

    public static array $FLOW = ["allowed_values" => ["Status" => ["pending", "shipped", "delivered", "cancelled"]], "transitions" => ["status" => ["pending" => ["shipped", "cancelled"], "shipped" => ["delivered"], "delivered" => []]]];

    public $id = 1;
    public $name = "John Doe";
    public $email = "john@doe.com";
    public $address;
    public \DateTimeImmutable $birthday;

    public function __construct() {
        $this->address = new address();
        $this->birthday = new \DateTimeImmutable('1990-01-01');
    }

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



class EmptyFormTestInstance {

    public static array $FLOW = ["allowed_values" => ["Status" => ["pending", "shipped", "delivered", "cancelled"]], "transitions" => ["status" => ["pending" => ["shipped", "cancelled"], "shipped" => ["delivered"], "delivered" => []]]];

    public $id;
    public $name;
    public $email;

    public $address;

    public \DateTimeImmutable $birthday;

    public function __construct() {
        $this->address = new address();
        $this->birthday = new \DateTimeImmutable('1990-01-01');
    }

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

    public function getBirthday(): ?\DateTimeImmutable
    {
        return $this->birthday;
    }

}


class AddressWithGetters
{
    public function __construct(
        private int $id = 100,
        private string $street = '456 Side St'
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStreet(): string
    {
        return $this->street;
    }
}


class GetterOnlyFormTestInstance
{
    public static array $FLOW = [
        "allowed_values" => ["Status" => ["pending", "shipped", "delivered", "cancelled"]],
        "transitions" => [
            "status" => [
                "pending" => ["shipped", "cancelled"],
                "shipped" => ["delivered"],
                "delivered" => [],
            ],
        ],
    ];

    private string $name = 'Getter Only';

    private string $email = 'getter@example.com';

    private AddressWithGetters $address;

    private \DateTimeImmutable $birthday;

    public function __construct()
    {
        $this->address = new AddressWithGetters();
        $this->birthday = new \DateTimeImmutable('1990-01-01');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAddress(): AddressWithGetters
    {
        return $this->address;
    }

    public function getBirthday(): \DateTimeImmutable
    {
        return $this->birthday;
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

    /**
     * @throws \Exception
     */
    public function testBuildNewUI()
    {
        $form = new Form(__DIR__ . '/data/form.xml');

        $json = new UIEngine();
        $json = $json->buildForm($form, new EmptyFormTestInstance());

        $decoded = json_decode($json, true);
        $prettyJson = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        file_put_contents(__DIR__ . '/data/BuiltForm.json', $prettyJson);
    }

    /**
     * @throws \Exception
     */
    public function testRJSFBuild()
    {
        $form = new Form(__DIR__ . '/data/category.xml');

        $engine = new UIEngine();
        $json = $engine->buildRJSFSchema($form, new EmptyFormTestInstance());


        file_put_contents(__DIR__ . '/data/RJSBuiltForm.json', $json);
    }

    public function testDateFieldValueIsFormatted()
    {
        $form = new Form(__DIR__ . '/data/form.xml');
        $engine = new UIEngine();

        $json = $engine->buildForm($form, new FormTestInstance());
        $decoded = json_decode($json, true);

        $birthdayField = $this->findFieldById($decoded['layout'] ?? [], 'birthday');

        $this->assertNotNull($birthdayField, 'The birthday field should exist in the generated layout.');
        $this->assertSame('1990-01-01', $birthdayField['value'] ?? null);
    }

    public function testValuesAreResolvedFromGetterMethods()
    {
        $form = new Form(__DIR__ . '/data/form.xml');
        $engine = new UIEngine();

        $json = $engine->buildForm($form, new GetterOnlyFormTestInstance());
        $decoded = json_decode($json, true);

        $nameField = $this->findFieldById($decoded['layout'] ?? [], 'name');
        $this->assertSame('Getter Only', $nameField['value'] ?? null, 'The name field should use the getter value.');

        $birthdayField = $this->findFieldById($decoded['layout'] ?? [], 'birthday');
        $this->assertSame('1990-01-01', $birthdayField['value'] ?? null, 'The birthday field should format the getter value.');

        $thumbField = $this->findFieldById($decoded['layout'] ?? [], 'thumb');
        $this->assertSame('100', $thumbField['value'] ?? null, 'Nested getter values should hydrate nested fields.');

        $streetField = $this->findFieldById($decoded['layout'] ?? [], 'address.street');
        $this->assertSame('456 Side St', $streetField['value'] ?? null, 'Nested getter values should hydrate dot notation fields.');
    }

    private function findFieldById(array $elements, string $id): ?array
    {
        foreach ($elements as $element) {
            if (($element['id'] ?? null) === $id || ($element['name'] ?? null) === $id) {
                return $element;
            }

            if (isset($element['elements']) && is_array($element['elements'])) {
                $found = $this->findFieldById($element['elements'], $id);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }
}



