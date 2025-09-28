<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use RocketPhp\RocketUI\Views\Service\PlaceholderReplacer;

class TestDataObject
{
    public $id = 123;
    public $name = "John Doe";
    public $email = "john@example.com";
    public $profile;

    public function __construct()
    {
        $this->profile = new TestNestedObject();
    }
}

class TestNestedObject
{
    public $id = 456;
    public $settings;

    public function __construct()
    {
        $this->settings = new TestDeepObject();
    }
}

class TestDeepObject
{
    public $theme = "dark";
    public $language = "en";
}

class PlaceholderReplacerTest extends TestCase
{
    public function testSimpleObjectReplacement()
    {
        $data = new TestDataObject();
        $text = "User ID: @@Rocket/id, Name: @@Rocket/name";

        $result = PlaceholderReplacer::replace($text, $data);

        $this->assertEquals("User ID: 123, Name: John Doe", $result);
    }

    public function testNestedObjectReplacement()
    {
        $data = new TestDataObject();
        $text = "Profile ID: @@Rocket/profile.id";

        $result = PlaceholderReplacer::replace($text, $data);

        $this->assertEquals("Profile ID: 456", $result);
    }

    public function testDeepNestedObjectReplacement()
    {
        $data = new TestDataObject();
        $text = "Theme: @@Rocket/profile.settings.theme";

        $result = PlaceholderReplacer::replace($text, $data);

        $this->assertEquals("Theme: dark", $result);
    }

    public function testArrayDataReplacement()
    {
        $data = [
            'id' => 789,
            'name' => 'Jane Smith',
            'profile' => [
                'id' => 101,
                'settings' => [
                    'theme' => 'light'
                ]
            ]
        ];

        $text = "User: @@Rocket/name (@@Rocket/id), Theme: @@Rocket/profile.settings.theme";

        $result = PlaceholderReplacer::replace($text, $data);

        $this->assertEquals("User: Jane Smith (789), Theme: light", $result);
    }

    public function testMissingFieldPreservesPlaceholder()
    {
        $data = new TestDataObject();
        $text = "Missing: @@Rocket/nonexistent";

        $result = PlaceholderReplacer::replace($text, $data);

        $this->assertEquals("Missing: @@Rocket/nonexistent", $result);
    }

    public function testMultipleReplacements()
    {
        $data = new TestDataObject();
        $text = "@@Rocket/name <@@Rocket/email> has ID @@Rocket/id";

        $result = PlaceholderReplacer::replace($text, $data);

        $this->assertEquals("John Doe <john@example.com> has ID 123", $result);
    }

    public function testReplaceInArray()
    {
        $data = new TestDataObject();
        $array = [
            'title' => 'User: @@Rocket/name',
            'url' => '/users/@@Rocket/id/edit',
            'nested' => [
                'description' => 'Profile ID: @@Rocket/profile.id'
            ],
            'number' => 42,
            'boolean' => true
        ];

        $result = PlaceholderReplacer::replaceInArray($array, $data);

        $expected = [
            'title' => 'User: John Doe',
            'url' => '/users/123/edit',
            'nested' => [
                'description' => 'Profile ID: 456'
            ],
            'number' => 42,
            'boolean' => true
        ];

        $this->assertEquals($expected, $result);
    }

    public function testEmptyData()
    {
        $text = "User: @@Rocket/name";

        $result = PlaceholderReplacer::replace($text, null);

        $this->assertEquals("User: @@Rocket/name", $result);
    }

    public function testNoPlaceholders()
    {
        $data = new TestDataObject();
        $text = "This is a normal string";

        $result = PlaceholderReplacer::replace($text, $data);

        $this->assertEquals("This is a normal string", $result);
    }

    public function testRedirectionExample()
    {
        $data = new TestDataObject();
        $redirection = "form/beneficiary/@@Rocket/id";

        $result = PlaceholderReplacer::replace($redirection, $data);

        $this->assertEquals("form/beneficiary/123", $result);
    }
}