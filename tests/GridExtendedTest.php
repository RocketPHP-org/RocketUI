<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use RocketPhp\RocketUI\Views\Grid\Grid;

// Import test classes from formTest.php and gridTest.php
require_once __DIR__ . '/formTest.php';
require_once __DIR__ . '/gridTest.php';
require_once __DIR__ . '/TestData.php';

class GridExtendedTest extends TestCase
{
    public function testGridWithTitleAttribute()
    {
        $grid = new Grid(__DIR__ . '/data/grid-with-placeholders.xml');
        $data = new TestUser();

        $this->assertEquals("Users for @@Rocket/company.name", $grid->getTitle());

        $json = $grid->buildGrid($data);
        $decoded = json_decode($json, true);

        $this->assertEquals("Users for Tech Corp", $decoded['title']);
    }

    public function testGridPlaceholderReplacement()
    {
        $grid = new Grid(__DIR__ . '/data/grid-with-placeholders.xml');
        $data = new TestUser();

        $json = $grid->buildGrid($data);
        $decoded = json_decode($json, true);

        // Test title replacement
        $this->assertEquals("Users for Tech Corp", $decoded['title']);

        // Test metadata replacement
        $this->assertEquals("Grid for Tech Corp", $decoded['metadata']['title']);
        $this->assertEquals("User management grid for company ID 100", $decoded['metadata']['description']);

        // Test actions replacement
        $addButton = null;
        $exportButton = null;
        foreach ($decoded['actions'] as $action) {
            if ($action['label'] === 'Add User') {
                $addButton = $action;
            } elseif ($action['label'] === 'Export') {
                $exportButton = $action;
            }
        }

        $this->assertNotNull($addButton);
        $this->assertEquals("users/create/100", $addButton['redirection']);

        $this->assertNotNull($exportButton);
        $this->assertEquals("users/export/100", $exportButton['redirection']);

        // Test row actions replacement
        $editButton = null;
        $deleteButton = null;
        foreach ($decoded['rowActions'] as $action) {
            if ($action['label'] === 'Edit') {
                $editButton = $action;
            } elseif ($action['label'] === 'Delete') {
                $deleteButton = $action;
            }
        }

        $this->assertNotNull($editButton);
        $this->assertEquals("users/42/edit", $editButton['redirection']);

        $this->assertNotNull($deleteButton);
        $this->assertEquals("users/42/delete", $deleteButton['redirection']);
        $this->assertEquals("Delete user Alice Johnson?", $deleteButton['confirm']);
    }

    public function testGridWithMissingPlaceholderData()
    {
        $grid = new Grid(__DIR__ . '/data/grid-with-placeholders.xml');
        $data = (object) ['id' => 999]; // Missing company and name

        $json = $grid->buildGrid($data);
        $decoded = json_decode($json, true);

        // Missing placeholders should remain unchanged
        $this->assertEquals("Users for @@Rocket/company.name", $decoded['title']);
        $this->assertEquals("Grid for @@Rocket/company.name", $decoded['metadata']['title']);
    }

    public function testGridWithArrayData()
    {
        $grid = new Grid(__DIR__ . '/data/grid-with-placeholders.xml');
        $data = (object) [
            'id' => 789,
            'name' => 'Charlie Brown',
            'company' => (object) [
                'id' => 200,
                'name' => 'Innovation Inc'
            ]
        ];

        $json = $grid->buildGrid($data);
        $decoded = json_decode($json, true);

        $this->assertEquals("Users for Innovation Inc", $decoded['title']);
        $this->assertEquals("Grid for Innovation Inc", $decoded['metadata']['title']);
        $this->assertEquals("User management grid for company ID 200", $decoded['metadata']['description']);
    }

    public function testOriginalGridStillWorks()
    {
        // Test that original grid.xml still works without issues
        $grid = new Grid(__DIR__ . '/data/grid.xml');
        $data = new FormTestInstance();

        $json = $grid->buildGrid($data);
        $decoded = json_decode($json, true);

        // Should have null title since original grid doesn't have title attribute
        $this->assertNull($decoded['title']);
    }
}