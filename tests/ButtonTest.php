<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use RocketPhp\RocketUI\Views\Form\Action\Button;

class ButtonTest extends TestCase
{
    public function testButtonWithRedirectionAttribute()
    {
        // Create a mock DOMElement with redirection attribute
        $xml = new \DOMDocument();
        $buttonElement = $xml->createElement('button');
        $buttonElement->setAttribute('type', 'primary');
        $buttonElement->setAttribute('confirm', 'Are you sure?');
        $buttonElement->setAttribute('redirection', '/users/123/edit');
        $buttonElement->setAttribute('id', 'test-button');
        $buttonElement->setAttribute('name', 'edit');
        $buttonElement->setAttribute('label', 'Edit User');

        $button = new Button($buttonElement);

        $this->assertEquals('primary', $button->getType());
        $this->assertEquals('Are you sure?', $button->getConfirm());
        $this->assertEquals('/users/123/edit', $button->getRedirection());
    }

    public function testButtonWithoutRedirectionAttribute()
    {
        $xml = new \DOMDocument();
        $buttonElement = $xml->createElement('button');
        $buttonElement->setAttribute('type', 'secondary');
        $buttonElement->setAttribute('id', 'test-button');
        $buttonElement->setAttribute('name', 'cancel');
        $buttonElement->setAttribute('label', 'Cancel');

        $button = new Button($buttonElement);

        $this->assertEquals('secondary', $button->getType());
        $this->assertNull($button->getConfirm());
        $this->assertNull($button->getRedirection());
    }

    public function testButtonJsonOutput()
    {
        $xml = new \DOMDocument();
        $buttonElement = $xml->createElement('button');
        $buttonElement->setAttribute('type', 'primary');
        $buttonElement->setAttribute('confirm', 'Delete this item?');
        $buttonElement->setAttribute('redirection', '/items/456/delete');
        $buttonElement->setAttribute('id', 'delete-button');
        $buttonElement->setAttribute('name', 'delete');
        $buttonElement->setAttribute('label', 'Delete');

        $button = new Button($buttonElement);
        $data = new \stdClass();

        $json = $button->getJson($data);

        $this->assertArrayHasKey('type', $json);
        $this->assertArrayHasKey('confirm', $json);
        $this->assertArrayHasKey('redirection', $json);

        $this->assertEquals('primary', $json['type']);
        $this->assertEquals('Delete this item?', $json['confirm']);
        $this->assertEquals('/items/456/delete', $json['redirection']);
        $this->assertEquals('Delete', $json['label']);
    }

    public function testButtonWithPlaceholderInRedirection()
    {
        $xml = new \DOMDocument();
        $buttonElement = $xml->createElement('button');
        $buttonElement->setAttribute('type', 'primary');
        $buttonElement->setAttribute('redirection', '/users/@@Rocket/id/edit');
        $buttonElement->setAttribute('id', 'edit-button');
        $buttonElement->setAttribute('name', 'edit');
        $buttonElement->setAttribute('label', 'Edit');

        $button = new Button($buttonElement);

        // The raw redirection should contain the placeholder
        $this->assertEquals('/users/@@Rocket/id/edit', $button->getRedirection());

        // When used in a form, the placeholder should be replaced by PlaceholderReplacer
        // This is tested in the FormExtendedTest and GridExtendedTest
    }

    public function testButtonEmptyRedirection()
    {
        $xml = new \DOMDocument();
        $buttonElement = $xml->createElement('button');
        $buttonElement->setAttribute('redirection', ''); // Empty redirection
        $buttonElement->setAttribute('id', 'test-button');

        $button = new Button($buttonElement);

        $this->assertNull($button->getRedirection());
    }
}