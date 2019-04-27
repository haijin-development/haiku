<?php

namespace Haijin\Haiku\Dom;

use Haijin\OrderedCollection;

class HaikuNode
{
    protected $childNodes;

    /// Initializing

    public function __construct()
    {
        $this->childNodes = new OrderedCollection();
    }

    /// Adding children

    public function addChild($child)
    {
        $this->childNodes->add($child);

        return $this;
    }

    /// Displaying

    public function indent($indentation)
    {
        return str_repeat("    ", $indentation);
    }

    protected function childNodesToHtml($indentation)
    {
        $html = "";

        foreach ($this->childNodes->toArray() as $i => $node) {

            $nodeHtml = $node->toHtml($indentation + 1);

            if (preg_match('/^<\?php +else/', $nodeHtml)) {

                $html = preg_replace('/\?>$/', '', $html);

                $nodeHtml = preg_replace('/^<\?php +else/', 'else', $nodeHtml);

            }

            $html .= $nodeHtml;

        }

        return $html;
    }

    protected function childNodesToPrettyHtml($indentation)
    {
        $html = "";

        foreach ($this->childNodes->toArray() as $i => $node) {

            $nodeHtml = $node->toPrettyHtml($indentation + 1);

            if (preg_match('/^<\?php +else/', $nodeHtml)) {

                $html = preg_replace('/\?>$/', '', $html);

                $nodeHtml = preg_replace('/^<\?php +else/', 'else', $nodeHtml);

            }

            $html .= $nodeHtml;

            $html .= "\n";
        }

        return $html;
    }
}