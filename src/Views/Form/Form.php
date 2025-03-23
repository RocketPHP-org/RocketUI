<?php
namespace RocketPhp\RocketUI\Views\Form;

use RocketPhp\RocketUI\Views\Form\Action\Button;
use RocketPhp\RocketUI\Views\Form\Layout\Layout;

class Form
{
    private array $metadata;
    private Layout $layout;
    private array $actions;

    /**
     * @throws \Exception
     */
    public function __construct(string $xmlPath)
    {
        $xml = new \DOMDocument();
        $xml->load($xmlPath);

        $this->layout = $this->parseLayout($xml);
        $this->metadata = $this->parseMetadata($xml);
        $this->actions = $this->parseActions($xml);
    }

    private function parseMetadata(\DOMDocument $xml): array
    {
        $metadataNode = $xml->getElementsByTagName("metadata")->item(0);
        if (!$metadataNode) {
            return [];
        }

        return [
            'title' => $metadataNode->getElementsByTagName("title")->item(0)?->nodeValue ?? '',
            'description' => $metadataNode->getElementsByTagName("description")->item(0)?->nodeValue ?? '',
            'version' => $metadataNode->getElementsByTagName("version")->item(0)?->nodeValue ?? '',
            'layout_type' => $this->getLayout()->getType(),
        ];
    }

    private function parseLayout(\DOMDocument $xml): Layout
    {
        $layoutNode = $xml->getElementsByTagName("layout")->item(0);
        if (!$layoutNode) {
            throw new \Exception("Aucune section <Layout> trouvée dans le XML.");
        }

        return new Layout($layoutNode);
    }

    private function parseActions(\DOMDocument $xml): array
    {
        $actions = [];
        $actionsNode = $xml->getElementsByTagName("actions")->item(0);
        if (!$actionsNode) {
            return [];
        }

        foreach ($actionsNode->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            switch ($child->nodeName) {
                case "button":
                    $actions[] = new Button($child);
                    break;
            }
        }

        return $actions;
    }

    public function getActions(): array
    {
        return $this->actions;
    }


    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getLayout(): Layout
    {
        return $this->layout;
    }

    public function buildForm(mixed $data)
    {
        $layout = $this->getLayout()->getJson($data);
        $actions = [];
        foreach ($this->actions as $action) {
            $actions[] = $action->getJson($data);
        }

        $jsonResponse = json_encode([
            'metadata' => $this->getMetadata(),
            'layout' => $layout,
            'actions' => $actions
        ]);

        return $jsonResponse;
    }
}