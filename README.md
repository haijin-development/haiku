# Haijin Haiku

The most simple template engine possible inspired in Ruby's [Slim](http://slim-lang.com/) syntax.

[![Latest Stable Version](https://poser.pugx.org/haijin/haiku/version)](https://packagist.org/packages/haijin/haiku)
[![Latest Unstable Version](https://poser.pugx.org/haijin/haiku/v/unstable)](https://packagist.org/packages/haijin/haiku)
[![Build Status](https://travis-ci.org/haijin-development/php-haiku.svg?branch=master)](https://travis-ci.org/haijin-development/php-haiku)
[![License](https://poser.pugx.org/haijin/haiku/license)](https://packagist.org/packages/haijin/haiku)

**Highlights**

* Minimalist syntax.
* Uses indentation instead of closing tags.
* Compiles `haiku` files to regular cached PHP files for efficiency.
* Minimum amount of features and logic. Delegates the inclusion of sub-templates to the view or application using the templates.

### Version 1.0.0

This library is under active development and no stable version was released yet.

If you like it a lot you may contribute by [financing](https://github.com/haijin-development/support-haijin-development) its development.

## Table of contents

1. [Installation](#c-1)
2. [Example](#c-2)
3. [Usage](#c-3)
    1. [Haiku syntax](#c-3-1)
        1. [Html tags](#c-3-1-1)
        2. [Tags attributes](#c-3-1-2)
        3. [Tags id and classes shortcuts](#c-3-1-3)
        4. [Text](#c-3-1-4)
        5. [PHP code evaluation](#c-3-1-5)
        6. [PHP code interpolation](#c-3-1-6)
        7. [Loops](#c-3-1-7)
        8. [Conditionals](#c-3-1-8)
        9. [Variables](#c-3-1-9)
    2. [Rendering](#c-3-2)
4. [Running the specs](#c-4)

<a name="c-1"></a>
## Installation

Include this library in your project `composer.json` file:

```json
{
    ...

    "require-dev": {
        ...
        "haijin/haiku": "^1.0",
        ...
    },

    ...
}
```
<a name="c-2"></a>
## Example

Example of a haiku template:

```
html
    head
    body.container

        div#haiku.poem data-id= "1"
            = "On the plum tree"
            br
            = "one blossom, one blossom worth"
            br
            = "of warmth."

        div.source data-id= "1", data-author= "Hattori Ransetsu"
            = "Hattori Ransetsu"
```

<a name="c-3"></a>
## Usage

<a name="c-3-1"></a>
### Haiku syntax

Haiku is a minimalist HTML template builder that uses indentation to avoid closing tags explicitly.

<a name="c-3-1-1"></a>
#### Html tags

Add the correct indentation and declare the tag name with no delimiter characters:

Example:

```
html lang = "en"
    head
        meta charset = "utf-8"
        meta name = "viewport", content="width=device-width, initial-scale=1, shrink-to-fit=no"

    link rel = "stylesheet",
        href = "https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"

    title
        = "Haiku template"

    body
        p
            = "On the plum tree"
        p
            = "one blossom, one blossom worth"
        p
            = "of warmth."

    script src = "https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
```

<a name="c-3-1-2"></a>
#### Tags attributes

The attribute name, the symbol `=` and the attribute value can have any number of optional spaces:

```
html lang = "en"
html lang= "en"
html lang="en"
```

Separate tag attributes with a  `,` char:

```
meta name = "viewport", content="width=device-width, initial-scale=1, shrink-to-fit=no"
```

Continue the attribute list in the line below after a `,` char.
The continued line can have any indentation.

```
meta name = "viewport",
        content="width=device-width, initial-scale=1, shrink-to-fit=no"
```

<a name="c-3-1-3"></a>
#### Tags id and classes shortcuts

Declare a tag `id` using the jquery shortcut:

```
body
    div#title
```

or even

```
body
    #title
```

for divs.

Declare a tag `class` using the jquery shortcut:

```
body
    div.row
        div.col.col-lg-2
```

or even

```
body
    .row
        .col.col-lg-2
```

for divs.

When declaring both a tag `id` and `class`, the tag `id` must go before the `class`:

```
div.row
    h1#title.col-lg-2
```

If the tag defines an `id` with both the jquery shortcut and its `id` attribute, the attribute has priority.

If the tag defines a `class` with both the jquery shortcut and its `class` attribute, the attribute `class` is merged into the jquery shortcut:

```
div.row
    h1#title.col-lg-2 id = "main-title", class = "main-title"
```

will render

```
<div class="row">
    <h1 id="main-title" class="col-lg-2 main-title">
    </h1>
<div>
```

<a name="c-3-1-4"></a>
#### Text

Everything after operands `=` and `!=` is plain PHP code that is evaluated and its result is rendered into the html.

Render text with operand `=`:

```
body
    p
        = "On the plum tree"
    p
        = "one blossom, one blossom worth"
    p
        = "of warmth."
```

`=` will escape html characters, so it's safe to render dynamic input from external sources like query parameters and databases.


Render unescaped text with operand `!=`:

```
body
    h1
        != "Haiku template"
```

Use unescaped text to render the contents of another template or dynamically generated html:

```
body
    != $view->renderTemplate( "subtemplate.haiku" )
```

<a name="c-3-1-5"></a>
#### PHP code evaluation

Evaluate a PHP line without rendering its result into the html with the `-` operand:

```
- $title = "Haiku template"
body
    h1
        = $title
```

Evaluate multiple lines of PHP code with:

```
- {{
    $title = "Haiku template";
    $haiku = [ "On the plum tree", "one blossom, one blossom worth", "of warmth." ];
}}

body
    h1
        = $title

    .poem
        - foreach( $haiku as $verse ) do
            p
                = $verse
```

<a name="c-3-1-6"></a>
#### PHP code interpolation

Interpolate PHP code in the attributes declarations with `{{}}`:

```
div.user-{{ $user->getId() }}
```

will output

```
<div class="user-1">
</div>
```

```
a href = {{ $view->getUrl() }}
```

will output

```
<a href="/...">
</a>
```

<a name="c-3-1-7"></a>
#### Loops

```
- foreach( $users as $user ) do
    tr
        td
            = $user->getName()
        td
            = $user->getLastname()
```

```
- while( $user !== null ) do
    tr
        td
            = $user->getName()
        td
            = $user->getLastname()
```

```
- for( $i; $i < 10; $i++ ) do
    tr
        td
            = $users[ $i ]->getName()
        td
            = $users[ $i ]->getLastname()
```

<a name="c-3-1-8"></a>
#### Conditionals

```
- if( $user !== null ) do
    div = $user->getName() . $user->getLastname()
```

```
- if( $user === null ) do

    = "No user id logged in"

- elseif( $user->isAdmin() ) do

    = "Admin: " . $user->getName() . $user->getLastname()

- else do

    = "User: " . $user->getName() . $user->getLastname()

```

As you might guess, any PHP conditional and loop statement is valid as long as it ends with a `do` statement instead of a `{` bracket.

<a name="c-3-1-9"></a>
#### Variables

Variables are referenced and assigned like in any PHP statement:

```
- $variable = 1;
```

```
- if ( $variable == 1 ) do
```

<a name="c-3-2"></a>
### Rendering

Render an input string on the fly with:

```php
use Haijin\Haiku\Renderer;

$renderer = new Renderer();

$input =
'html
    body
        = "A haiku template from {$author}"
';

$html = $renderer->render( $input, [
    "author" => "Haijin Development"
]);
```

Render an input file with:

```php
use Haijin\Haiku\Renderer;

$renderer = new Renderer();

$renderer->configure( function($renderer) {

    $renderer->cacheFolder = "php-templates-cache";

});

$html = $renderer->renderFile( "sample.haiku.html", [
    "author" => "Haijin Development"
]);
```

#### How does the rendering work

The renderer translates haiku template files of the form

```
'html
    body
        = "A haiku template from {$author}"
'
```

to PHP scripts of the form:

```
<html><body><?php echo htmlspecialchars( "A haiku template from {$author}" ); ?></body></html>
```

and stores them in the configured cache folder.

Later requests to parse the `sample.haiku.html` file are not translated again, instead the PHP cached file is evaluated to generate the HTML unless its haiku source file changed.

This makes `haijin/haiku` pretty efficient to render its templates as it relies directly on the evaluation of PHP scripts with all of its optimizations.

<a name="c-4"></a>
## Running the specs

```
composer specs
```

Or if you want to run the tests using a Docker with PHP 7.2:

```
sudo docker run -ti -v $(pwd):/home/php-haiku --rm --name php-haiku haijin/php-dev:7.2 bash
cd /home/php-haiku/
composer install
composer specs
```