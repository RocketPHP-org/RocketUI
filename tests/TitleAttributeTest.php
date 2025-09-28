<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use RocketPhp\RocketUI\Views\Form\Form;
use RocketPhp\RocketUI\Views\Grid\Grid;

// Import test classes from formTest.php
require_once __DIR__ . '/formTest.php';
require_once __DIR__ . '/TestData.php';

class TitleAttributeTest extends TestCase
{
    public function testFormTitleAttribute()
    {
        $form = new Form(__DIR__ . '/data/form-with-placeholders.xml');

        // Test that title is extracted from XML attribute
        $this->assertEquals("Edit User @@Rocket/name", $form->getTitle());
    }

    public function testFormWithoutTitleAttribute()
    {
        $form = new Form(__DIR__ . '/data/form.xml');

        // Original form doesn't have title attribute, should be null
        $this->assertNull($form->getTitle());
    }

    public function testFormTitleInJsonOutput()
    {
        $form = new Form(__DIR__ . '/data/form-with-placeholders.xml');
        $data = new TestUser();

        $json = $form->buildForm($data);
        $decoded = json_decode($json, true);

        // Title should be included in JSON output and placeholders replaced
        $this->assertArrayHasKey('title', $decoded);
        $this->assertEquals("Edit User Alice Johnson", $decoded['title']);
    }

    public function testFormWithoutTitleInJsonOutput()
    {
        $form = new Form(__DIR__ . '/data/form.xml');
        $data = new FormTestInstance();

        $json = $form->buildForm($data);
        $decoded = json_decode($json, true);

        // Title should be null in JSON output
        $this->assertArrayHasKey('title', $decoded);
        $this->assertNull($decoded['title']);
    }

    public function testGridTitleAttribute()
    {
        $grid = new Grid(__DIR__ . '/data/grid-with-placeholders.xml');

        // Test that title is extracted from XML attribute
        $this->assertEquals("Users for @@Rocket/company.name", $grid->getTitle());
    }

    public function testGridWithoutTitleAttribute()
    {
        $grid = new Grid(__DIR__ . '/data/grid.xml');

        // Original grid doesn't have title attribute, should be null
        $this->assertNull($grid->getTitle());
    }

    public function testGridTitleInJsonOutput()
    {
        $grid = new Grid(__DIR__ . '/data/grid-with-placeholders.xml');
        $data = new TestUser();

        $json = $grid->buildGrid($data);
        $decoded = json_decode($json, true);

        // Title should be included in JSON output and placeholders replaced
        $this->assertArrayHasKey('title', $decoded);
        $this->assertEquals("Users for Tech Corp", $decoded['title']);
    }

    public function testGridWithoutTitleInJsonOutput()
    {
        $grid = new Grid(__DIR__ . '/data/grid.xml');
        $data = new FormTestInstance();

        $json = $grid->buildGrid($data);
        $decoded = json_decode($json, true);

        // Title should be null in JSON output
        $this->assertArrayHasKey('title', $decoded);
        $this->assertNull($decoded['title']);
    }

    public function testEmptyTitleAttribute()
    {
        // Create a temporary XML with empty title
        $xmlContent = '<RocketForm xmlns="http://rocket.com/rf-schema" title="">
            <metadata><title>Test</title></metadata>
            <layout type="modal"></layout>
            <actions></actions>
        </RocketForm>';

        $tempFile = tempnam(sys_get_temp_dir(), 'test_form');
        file_put_contents($tempFile, $xmlContent);

        try {
            $form = new Form($tempFile);
            $this->assertNull($form->getTitle());
        } finally {
            unlink($tempFile);
        }
    }

    public function testTitleWithSpecialCharacters()
    {
        // Create a temporary XML with special characters in title
        $xmlContent = '<RocketForm xmlns="http://rocket.com/rf-schema" title="Édition Utilisateur @@Rocket/name - Spécial &amp; Caractères">
            <metadata><title>Test</title></metadata>
            <layout type="modal"></layout>
            <actions></actions>
        </RocketForm>';

        $tempFile = tempnam(sys_get_temp_dir(), 'test_form');
        file_put_contents($tempFile, $xmlContent);

        try {
            $form = new Form($tempFile);
            $this->assertEquals('Édition Utilisateur @@Rocket/name - Spécial & Caractères', $form->getTitle());
        } finally {
            unlink($tempFile);
        }
    }
}