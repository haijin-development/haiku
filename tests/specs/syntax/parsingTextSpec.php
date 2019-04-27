<?php

use Haijin\Haiku\HaikuParserDefinition;
use Haijin\Parser\Parser;

$spec->describe("When parsing text", function () {

    $this->let("parser", function () {

        return new Parser(HaikuParserDefinition::$definition);

    });

    $this->describe("escaped text with = and no ending semicolon", function () {

        $this->let("input", function () {
            return
                "div
    = '123'
";
        });

        $this->let("expectedHtml", function () {
            return
                "<div><?php echo htmlspecialchars( '123' ); ?></div>";
        });

        $this->it("generates the escaped text", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("escaped text with = and ending semicolon", function () {

        $this->let("input", function () {
            return
                "div
    = '123';
";
        });

        $this->let("expectedHtml", function () {
            return
                "<div><?php echo htmlspecialchars( '123' ); ?></div>";
        });

        $this->it("generates the escaped text", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });


    $this->describe("unescaped text with != and no ending semicolon", function () {

        $this->let("input", function () {
            return
                "div
    != '123'
";
        });

        $this->let("expectedHtml", function () {
            return
                "<div><?php echo '123'; ?></div>";
        });

        $this->it("generates the unescaped text", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("unescaped text with == and ending semicolon", function () {

        $this->let("input", function () {
            return
                "div
    != '123';
";
        });

        $this->let("expectedHtml", function () {
            return
                "<div><?php echo '123'; ?></div>";
        });

        $this->it("generates the unescaped text", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });


    $this->describe("escaped text with = and no ending cr", function () {

        $this->let("input", function () {
            return
                "div
    = '123'";
        });

        $this->let("expectedHtml", function () {
            return
                "<div><?php echo htmlspecialchars( '123' ); ?></div>";
        });

        $this->it("generates the escaped text", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("escaped multiline text", function () {

        $this->let("input", function () {
            return
                "div
    = {{
        '123' .
'       '321';
    }}";
        });

        $this->let("expectedHtml", function () {
            return
                "<div><?php echo htmlspecialchars( '123' .
'       '321' ); ?></div>";
        });

        $this->it("generates the escaped text", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("unescaped text with != and no ending cr", function () {

        $this->let("input", function () {
            return
                "div
    != '123'";
        });

        $this->let("expectedHtml", function () {
            return
                "<div><?php echo '123'; ?></div>";
        });

        $this->it("generates the escaped text", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("unescaped text with != and no ending cr", function () {

        $this->let("input", function () {
            return
                "div
    != '123'";
        });

        $this->let("expectedHtml", function () {
            return
                "<div>
    <?php echo '123'; ?>
</div>
";
        });

        $this->it("generates the escaped text", function () {

            $html = $this->parser->parseString($this->input)->toPrettyHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("unescaped multiline text", function () {

        $this->let("input", function () {
            return
                "div
    != {{
        '123' .
'       '321';
    }}";
        });

        $this->let("expectedHtml", function () {
            return
                "<div><?php echo '123' .
'       '321'; ?></div>";
        });

        $this->it("generates the escaped text", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

});