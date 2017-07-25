<?php
namespace GraphQL\Parser;

use Masterminds\HTML5;

class HTML {
    private $dom;
    private $data = [];

    public function __construct($html) {
        ini_set('html_errors', 0);
        $doc = new HTML5();
        $trimmed = trim($html);
        $this->dom = $doc->loadHTMLFragment($trimmed);
        $this->walkNode($this->data, $this->dom);
    }

    public function getData() {
        return $this->data;
    }

    private function walkNode(&$data, $node) {
        foreach ($node->childNodes as $node) {
            $parsed = [];
            if ($node instanceof \DOMElement) {
                $parsed['tagName'] = $node->tagName;
                if (! empty($node->attributes)) {
                    $parsed['attributes'] = [];
                    foreach ($node->attributes as $a) {
                        $parsed['attributes'][] = [
                            'name' => $a->name,
                            'value' => $a->value,
                        ];
                    }
                }
                if ($node->hasChildNodes()) {
                    $parsed['children'] = [];
                    $this->walkNode($parsed['children'], $node);
                }
            } elseif ($node instanceof \DOMText) {
                $trimmed = trim($node->wholeText);
                if (empty($trimmed)) {
                    continue;
                }
                $parsed['text'] = $trimmed;
            }
            $data[] = $parsed;
        }
    }
}
