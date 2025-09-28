<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use RocketPhp\RocketUI\Views\Form\Form;

// Import test classes from formTest.php
require_once __DIR__ . '/formTest.php';
require_once __DIR__ . '/TestData.php';

class FormExtendedTest extends TestCase
{
    public function testFormWithTitleAttribute()
    {
        $form = new Form(__DIR__ . '/data/form-with-placeholders.xml');
        $data = new TestUser();

        $this->assertEquals("Edit User @@Rocket/name", $form->getTitle());

        $json = $form->buildForm($data);
        $decoded = json_decode($json, true);

        $this->assertEquals("Edit User Alice Johnson", $decoded['title']);
    }

    public function testFormPlaceholderReplacement()
    {
        $form = new Form(__DIR__ . '/data/form-with-placeholders.xml');
        $data = new TestUser();

        $json = $form->buildForm($data);
        $decoded = json_decode($json, true);

        // Test title replacement
        $this->assertEquals("Edit User Alice Johnson", $decoded['title']);

        // Test metadata replacement
        $this->assertEquals("Form for Alice Johnson", $decoded['metadata']['title']);
        $this->assertEquals("Edit user details for ID 42", $decoded['metadata']['description']);

        // Test action redirection replacement
        $submitButton = null;
        $cancelButton = null;
        foreach ($decoded['actions'] as $action) {
            if ($action['name'] === 'submit') {
                $submitButton = $action;
            } elseif ($action['label'] === 'Cancel') {
                $cancelButton = $action;
            }
        }

        $this->assertNotNull($submitButton);
        $this->assertEquals("users/42/view", $submitButton['redirection']);
        $this->assertEquals("Save changes for Alice Johnson?", $submitButton['confirm']);

        $this->assertNotNull($cancelButton);
        $this->assertEquals("users/list", $cancelButton['redirection']);
    }

    public function testFormWithMissingPlaceholderData()
    {
        $form = new Form(__DIR__ . '/data/form-with-placeholders.xml');
        $data = (object) ['id' => 999]; // Missing name and email

        $json = $form->buildForm($data);
        $decoded = json_decode($json, true);

        // Missing placeholders should remain unchanged
        $this->assertEquals("Edit User @@Rocket/name", $decoded['title']);
        $this->assertEquals("Form for @@Rocket/name", $decoded['metadata']['title']);
    }

    public function testFormWithArrayData()
    {
        $form = new Form(__DIR__ . '/data/form-with-placeholders.xml');
        $data = (object) [
            'id' => 123,
            'name' => 'Bob Smith',
            'email' => 'bob@example.com'
        ];

        $json = $form->buildForm($data);
        $decoded = json_decode($json, true);

        $this->assertEquals("Edit User Bob Smith", $decoded['title']);
        $this->assertEquals("Form for Bob Smith", $decoded['metadata']['title']);
        $this->assertEquals("Edit user details for ID 123", $decoded['metadata']['description']);
    }

    public function testOriginalFormStillWorks()
    {
        // Test that original form.xml still works without issues
        $form = new Form(__DIR__ . '/data/form.xml');
        $data = new FormTestInstance();

        $json = $form->buildForm($data);
        $decoded = json_decode($json, true);

        // Should have null title since original form doesn't have title attribute
        $this->assertNull($decoded['title']);
        $this->assertEquals("Test Form", $decoded['metadata']['title']);
    }
}