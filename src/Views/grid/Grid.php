<?php

namespace RocketPhp\RocketUI\Views\Grid;

use RocketPhp\RocketUI\Views\Grid\Action\Button;
use RocketPhp\RocketUI\Views\Form\Layout\Layout;
use RocketPhp\RocketUI\Views\Grid\Column\Column;
use RocketPhp\RocketUI\Views\Grid\Toggle\Toggle;
use RocketPhp\RocketUI\Views\Service\PlaceholderReplacer;

class Grid
{
    private array $metadata;
    private array $toggles;

    private array $columns;
    private array $actions;
    private array $gridOptions;
    private array $rowActions;
    private ?string $title;

    /**
     * @throws \Exception
     */
    public function __construct(string $xmlPath)
    {
        $xml = new \DOMDocument();
        $xml->load($xmlPath);

        $this->title = $xml->documentElement->getAttribute('title') ?: null;
        $this->metadata = $this->parseMetadata($xml);
        $this->toggles = $this->parseToggles($xml);
        $this->columns = $this->parseColumns($xml);
        $this->actions = $this->parseActions($xml);
        $this->gridOptions = $this->parseGridOptions($xml);
        $this->rowActions = $this->parseRowActions($xml);

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
            'version' => $metadataNode->getElementsByTagName("version")->item(0)?->nodeValue ?? ''
        ];
    }

    private function parseToggles(\DOMDocument $xml): array
    {
        $toggles = [];
        $togglesNode = $xml->getElementsByTagName("toggles")->item(0);
        if (!$togglesNode) {
            return [];
        }

        foreach ($togglesNode->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            switch ($child->nodeName) {
                case "toggle":
                    $toggles[] = new Toggle($child);
                    break;
            }
        }

        return $toggles;
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

    private function parseColumns(\DOMDocument $xml): array
    {
        $columns = [];
        $columnsNode = $xml->getElementsByTagName("columns")->item(0);
        if (!$columnsNode) {
            return [];
        }

        foreach ($columnsNode->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            switch ($child->nodeName) {
                case "column":
                    $columns[] = new Column($child);
                    break;
            }
        }

        return $columns;
    }


    private function parseGridOptions(\DOMDocument $xml): array
    {
        $node = $xml->getElementsByTagName("gridOptions")->item(0);
        if (!$node) {
            return [];
        }

        $gridOption = new GridOption($node);
        return $gridOption->getJson();
    }

    private function parseRowActions(\DOMDocument $xml): array
    {
        $rowActions = [];
        $rowActionsNode = $xml->getElementsByTagName("rowActions")->item(0);
        if (!$rowActionsNode) {
            return [];
        }

        foreach ($rowActionsNode->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            if ($child->nodeName === "button") {
                $rowActions[] = new Button($child); // ou RowActionButton
            }
        }

        return $rowActions;
    }


    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getToggles(): array
    {
        return $this->toggles;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function buildGrid(mixed $data): string
    {
        $toggle = array_map(fn($toggle) => $toggle->getJson(), $this->toggles);
        $columns = array_map(fn($col) => $col->getJson($data), $this->columns);
        $actions = array_map(fn($btn) => $btn->getJson($data), $this->actions);
        $rowActions = array_map(fn($btn) => $btn->getJson($data), $this->rowActions ?? []);
        $gridOptions = $this->gridOptions ?? [];

        $responseArray = [
            'title' => $this->getTitle(),
            'metadata' => $this->getMetadata(),
            'toggles' => $toggle,
            'columns' => $columns,
            'actions' => $actions,
            'rowActions' => $rowActions,
            'gridOptions' => $gridOptions,
        ];

        $responseArray = PlaceholderReplacer::replaceInArray($responseArray, $data);

        return json_encode($responseArray);
    }

}