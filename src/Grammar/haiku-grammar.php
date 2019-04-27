<?php

namespace Haijin\Haiku\Grammar;

use Haijin\Haiku\Dom\HaikuBrackedStatement;
use Haijin\Haiku\Dom\HaikuDocument;
use Haijin\Haiku\Dom\HaikuPHPEchoedExpression;
use Haijin\Haiku\Dom\HaikuPHPExpression;
use Haijin\Haiku\Dom\HaikuTag;
use Haijin\Haiku\Errors\IndentationCharMissmatchError;
use Haijin\Haiku\Errors\InvalidIndentationIncrementError;
use Haijin\Haiku\Errors\NotUniqueIndentationCharError;
use Haijin\Haiku\Errors\UnmatchedIndentationError;
use Haijin\OrderedCollection;


$parser->beforeParsing(function () {

    $this->indentationUnit = null;
    $this->indentationChar = null;

});

/// Root

$parser->expression("root", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp->linesList();

    });

    $exp->handler(function ($nodesList) {

        $document = new HaikuDocument();

        $nodesList->eachDo(function ($node) use ($document) {

            $document->addChild($node);

        });

        return $document;

    });

});


/// Lines

$parser->expression("linesList", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp
            ->line()->cr()->linesList()
            ->or()
            ->line()->eol();

    });

    $exp->handler(function ($node, $nodesList = null) {

        if ($node === null) {
            return $nodesList;
        }

        $nodes = new OrderedCollection();

        if ($nodesList === null) {

            $nodes->add($node);

            return $nodes;
        }

        $previousLineNode = $nodesList->first();

        if ($node->indentation == $previousLineNode->indentation) {

            $nodes->add($node);

            $nodesList->eachDo(function ($eachNode) use ($node, $nodes) {

                if ($node->indentation < $eachNode->indentation) {
                    return $this->raiseUnexpectedExpressionError();
                }

                $nodes->add($eachNode);

            });

            return $nodes;

        }

        if ($node->indentation < $previousLineNode->indentation) {

            $nodes->add($node);

            $nodesList->eachDo(function ($eachNode) use ($node, $nodes) {

                if ($node->indentation < $eachNode->indentation - 1) {
                    $this->raiseInvalidIndentationIncrementError();
                }

                if ($node->indentation == $eachNode->indentation - 1) {
                    $node->addChild($eachNode);
                }

                if ($node->indentation >= $eachNode->indentation) {
                    $nodes->add($eachNode);
                }

            });

            return $nodes;

        }

        if ($node->indentation > $previousLineNode->indentation) {

            $nodes->add($node);

            $nodesList->eachDo(function ($eachNode) use ($node, $nodes) {

                if ($node->indentation == $eachNode->indentation) {
                    throw new HaijinError("Invalid indentation found");
                }

                if ($node->indentation < $eachNode->indentation) {
                    throw new HaijinError("Invalid indentation found");
                }

                $nodes->add($eachNode);

            });

            return $nodes;

        }

    });

});

$parser->expression("line", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp->indentation()->opt($exp->statement());


    });

    $exp->handler(function ($indentation, $tagNode = null) {

        if ($tagNode !== null) {
            $tagNode->indentation = $indentation;
        }

        return $tagNode;

    });

});

$parser->expression("statement", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp
            ->tag()
            ->or()
            ->brackedStatement()
            ->or()
            ->unescapedText()
            ->or()
            ->escapedText()
            ->or()
            ->phpStatement();

    });

    $exp->handler(function ($tagNode = null) {

        return $tagNode;

    });

});

/// Indentation

$parser->expression("indentation", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp->regex("/((?: |\t)*)(?! |\t)/");

    });

    $exp->handler(function ($spaces) {

        if (preg_match("/\t/", $spaces) && preg_match("/ /", $spaces)) {
            $this->raiseNotUniqueIndentationCharError();
        }

        $spacesCount = strlen($spaces);

        if ($this->indentationUnit == null && $spacesCount > 0) {

            $this->indentationUnit = $spacesCount;
            $this->indentationChar = $spaces[0];

        }

        if ($this->indentationChar == " " && preg_match("/\t/", $spaces)) {
            $this->raiseIndentationCharMissmatchError("spaces", "tabs");
        }
        if ($this->indentationChar == "\t" && preg_match("/ /", $spaces)) {
            $this->raiseIndentationCharMissmatchError("tabs", "spaces");
        }

        if ($spacesCount > 0 && $spacesCount % $this->indentationUnit != 0) {
            $this->raiseUnmatchedIndentationError(
                $spacesCount, $this->indentationUnit
            );
        }

        if ($spacesCount == 0) {
            $indentation = 0;
        } else {
            $indentation = $spacesCount / $this->indentationUnit;
        }

        return $indentation;

    });

});

/// Tags

$parser->expression("tag", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp
            ->explicitTag()
            ->or()
            ->implicitDiv();

    });

    $exp->handler(function ($tagNode) {
        return $tagNode;
    });

});

$parser->expression("explicitTag", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp
            ->tagName()
            ->opt($exp->jqueryId())
            ->opt($exp->jqueryClasses())
            ->space()
            ->opt($exp->tagAttributesList());

    });

    $exp->handler(function ($tagName, $tagId, $tagClasses, $attributes) {

        $tagNode = new HaikuTag($tagName);

        if ($attributes === null) {
            $attributes = [];
        }

        if ($tagId !== null && !isset($attributes["id"])) {
            $tagNode->setAttribute("id", $tagId);
        }

        if ($tagClasses !== null) {
            if (isset($attributes["class"])) {
                $attributes["class"] = $tagClasses . " " . $attributes["class"];
            } else {
                $attributes["class"] = $tagClasses;
            }
        }

        foreach ($attributes as $name => $value) {
            $tagNode->setAttribute($name, $value);
        }

        return $tagNode;
    });

});

$parser->expression("implicitDiv", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp
            ->jqueryId()
            ->opt($exp->jqueryClasses())
            ->space()
            ->opt($exp->tagAttributesList())
            ->or()
            ->opt($exp->jqueryId())
            ->jqueryClasses()
            ->space()
            ->opt($exp->tagAttributesList());

    });

    $exp->handler(function ($tagId, $tagClasses, $attributes) {

        $tagNode = new HaikuTag("div");

        if ($attributes === null) {
            $attributes = [];
        }

        if ($tagId !== null && !isset($attributes["id"])) {
            $tagNode->setAttribute("id", $tagId);
        }

        if ($tagClasses !== null) {
            if (isset($attributes["class"])) {
                $attributes["class"] = $tagClasses . " " . $attributes["class"];
            } else {
                $attributes["class"] = $tagClasses;
            }
        }

        foreach ($attributes as $name => $value) {
            $tagNode->setAttribute($name, $value);
        }

        return $tagNode;
    });

});

$parser->expression("tagName", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp->regex("/([0-9a-zA-z]+)/");

    });

    $exp->handler(function ($tagString) {

        return $tagString;

    });

});

$parser->expression("jqueryId", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp->str("#")->htmlName();

    });

    $exp->handler(function ($id) {

        return $id;

    });

});

$parser->expression("jqueryClasses", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp
            ->jqueryClass()->jqueryClasses()
            ->or()
            ->jqueryClass();

    });

    $exp->handler(function ($class, $classes = null) {

        if ($classes === null) {
            return $class;
        }


        return $class . " " . $classes;

    });

});

$parser->expression("jqueryClass", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp->str(".")->htmlName();

    });

    $exp->handler(function ($class) {

        return $class;

    });

});

$parser->expression("tagAttributesList", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp
            ->attribute()->space()->str(",")->blank()->tagAttributesList()
            ->or()
            ->attribute();

    });

    $exp->handler(function ($attribute, $attributeList = null) {

        if ($attributeList === null) {
            return $attribute;
        }

        return array_merge($attribute, $attributeList);

    });

});

$parser->expression("attribute", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp
            ->htmlName()
            ->space()->str("=")->space()
            ->attributeValue();

    });

    $exp->handler(function ($name, $value) {

        return [$name => $value];

    });

});

$parser->expression("attributeValue", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp->stringLiteral();

    });

    $exp->handler(function ($string) {

        return $string;

    });

});

/// Statements

$parser->expression("brackedStatement", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp->regex("/-(.+) do/")->space();

    });

    $exp->handler(function ($text) {

        return new HaikuBrackedStatement(trim($text));

    });

});

$parser->expression("unescapedText", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp
            ->regex("/!=\s*\{\{(.+)\}\}/sU")->space()
            ->or()
            ->regex("/!=(.+)(?=\n|$)/");

    });

    $exp->handler(function ($text) {

        return new HaikuPHPEchoedExpression(trim($text), false);

    });

});

$parser->expression("escapedText", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp
            ->regex("/=\s*\{\{(.+)\}\}/sU")->space()
            ->or()
            ->regex("/=(.+)(?=\n|$)/");

    });

    $exp->handler(function ($text) {

        return new HaikuPHPEchoedExpression(trim($text));

    });

});

$parser->expression("phpStatement", function ($exp) {

    $exp->matcher(function ($exp) {

        $exp
            ->regex("/-\s*\{\{(.+)\}\}/sU")->space()
            ->or()
            ->regex("/-(.+)(?=\n|$)/");

    });

    $exp->handler(function ($text) {

        return new HaikuPHPExpression(trim($text));

    });

});

/// Interpolated expressions

$parser->expression("htmlName", function ($exp) {

    $exp->processor(function () {

        $char = $this->peekChar();

        if (!ctype_alnum($char) && $char != "-" && $char != "_" && $char != "{") {
            return false;
        }

        $literal = "";

        while ($this->notEndOfStream()) {

            $char = $this->nextChar();

            if ($char == "{" && $this->peekChar(1) == "{") {
                $literal .= "<?php echo htmlspecialchars(";

                $char = $this->nextChar();

                while ($char != "}" && $this->peekChar(1) != "}") {
                    $char = $this->nextChar();

                    $literal .= $char;
                }

                $literal .= "); ?>";

                $this->skipChars(2);

                $char = $this->nextChar();
            }

            if (!ctype_alnum($char) && $char != "-" && $char != "_") {
                $this->skipChars(-1);
                break;
            }

            $literal .= \htmlspecialchars($char);
        }

        $this->setResult($literal);

        return true;

    });

    $exp->handler(function ($tagString) {

        return $tagString;

    });

});

$parser->expression("stringLiteral", function ($exp) {

    $exp->processor(function () {

        if ($this->peekChar() != '"') {
            return false;
        }

        $this->skipChars(1);

        $currentLiteral = "";

        $scapingNext = false;

        while ($this->notEndOfStream()) {

            $char = $this->nextChar();

            if ($scapingNext === true) {
                $currentLiteral .= \htmlspecialchars($char);

                $scapingNext = false;
                continue;
            }

            if ($char == '\\') {
                $scapingNext = true;
                continue;
            }

            if ($char == '"') {
                break;
            }

            if ($char == "{" && $this->peekChar(1) == "{") {
                $currentLiteral .= "<?php echo htmlspecialchars(";

                $char = $this->nextChar();

                while ($char != "}" && $this->peekChar(1) != "}") {
                    $char = $this->nextChar();

                    $currentLiteral .= $char;
                }

                $currentLiteral .= "); ?>";

                $this->skipChars(2);

                $char = $this->nextChar();

                if ($char == '"') {
                    break;
                }
            }

            $currentLiteral .= \htmlspecialchars($char);
        }

        $this->setResult($currentLiteral);

        return true;

    });

    $exp->handler(function ($string) {

        return $string;

    });

});

/// Custom methods

$parser->def("raiseUnmatchedIndentationError", function ($spacesCount, $unit) {

    if ($this->indentationChar == "\t") {
        $char = "tabs";
    } else {
        $char = "spaces";
    };

    throw new UnmatchedIndentationError(
        "The template is using indentation units of {$unit} {$char}, but a line with {$spacesCount} {$char} was found. At line: {$this->currentLine()} column: {$this->currentColumn()}."
    );

});

$parser->def("raiseNotUniqueIndentationCharError", function () {

    throw new NotUniqueIndentationCharError(
        "The template is using both tabs and spaces to indent, use only tabs or only spaces. At line: {$this->currentLine()} column: {$this->currentColumn()}."
    );

});

$parser->def("raiseIndentationCharMissmatchError", function ($usedChars, $missmatchedChars) {

    throw new IndentationCharMissmatchError(
        "The template is indenting with {$usedChars} in one line and {$missmatchedChars} in another one, use only tabs or only spaces in all lines. At line: {$this->currentLine()} column: {$this->currentColumn()}."
    );

});

$parser->def("raiseInvalidIndentationIncrementError", function () {

    $lineIndex = $this->currentLine() - 1;

    throw new InvalidIndentationIncrementError(
        "Invalid indentation was found. An increment of only one unit was expected. At line: {$lineIndex} column: {$this->currentColumn()}."
    );

});
