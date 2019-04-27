<?php

namespace Haijin\Haiku\Dom;

use Haijin\Dictionary;

class HaikuTag extends HaikuNode
{
    protected $tag;
    protected $attributes;

    public function __construct($tag = null)
    {
        parent::__construct();

        $this->tag = $tag;
        $this->attributes = new Dictionary();
    }

    public function setAttribute($attributeName, $attributeValue)
    {
        $this->attributes[$attributeName] = $attributeValue;
    }

    public function toHtml($indentation)
    {
        $html = "<{$this->tag}";

        $html .= $this->attributesToHtml();

        if ($this->childNodes->isEmpty()) {

            $html .= " />";

        } else {

            $html .= ">";

            $html .= $this->childNodesToHtml($indentation);

            $html .= "</{$this->tag}>";

        }

        return $html;
    }

    public function toPrettyHtml($indentation)
    {
        $html = $this->indent($indentation) . "<{$this->tag}";

        $html .= $this->attributesToHtml();

        if ($this->childNodes->isEmpty()) {

            $html .= " />";

        } else {

            $html .= ">" . "\n";

            $html .= $this->childNodesToPrettyHtml($indentation);

            $html .= $this->indent($indentation) . "</{$this->tag}>";

        }

        return $html;
    }

    protected function attributesToHtml()
    {
        if ($this->attributes->isEmpty()) {
            return "";
        }

        $strings = [];

        foreach ($this->attributes->toArray() as $name => $value) {

            $strings[] =
                $name
                . "=" .
                '"' . $value . '"';

        }

        return " " . join(" ", $strings);
    }
}