<?php

namespace RocketPhp\RocketUI\Views\Grid;

class GridOption
{
    public function __construct(private \DOMElement $node) {}

    public function getJson(): array
    {
        $result = [];

        $paginationNode = $this->node->getElementsByTagName("pagination")->item(0);
        if ($paginationNode) {
            $result['pagination'] = [
                'pageSize' => $paginationNode->getAttribute('pageSize'),
                'pageSizeOptions' => explode(',', $paginationNode->getAttribute('pageSizeOptions')),
            ];
        }

        $selectionNode = $this->node->getElementsByTagName("selection")->item(0);
        if ($selectionNode) {
            $result['selection'] = [
                'mode' => $selectionNode->getAttribute('mode'),
            ];
        }

        $exportNode = $this->node->getElementsByTagName("export")->item(0);
        if ($exportNode) {
            $formats = [];
            foreach ($exportNode->getElementsByTagName("format") as $format) {
                $formats[] = $format->nodeValue;
            }
            $result['export'] = ['formats' => $formats];
        }

        return $result;
    }
}