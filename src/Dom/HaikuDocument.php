<?php

namespace Haijin\Haiku\Dom;

class HaikuDocument extends HaikuNode
{
    public function toHtml()
    {
        return $this->childNodesToHtml(-1);
    }

    public function toPrettyHtml()
    {
        return $this->childNodesToPrettyHtml(-1);
    }
}