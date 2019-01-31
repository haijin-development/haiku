# Haijin Haiku

The most simple template engine possible, inspired in Ruby's Slim.

[![Latest Stable Version](https://poser.pugx.org/haijin/haiku/version)](https://packagist.org/packages/haijin/haiku)
[![Latest Unstable Version](https://poser.pugx.org/haijin/haiku/v/unstable)](https://packagist.org/packages/haijin/haiku)
[![Build Status](https://travis-ci.org/haijin-development/php-haiku.svg?branch=master)](https://travis-ci.org/haijin-development/php-haiku)
[![License](https://poser.pugx.org/haijin/haiku/license)](https://packagist.org/packages/haijin/haiku)

### Version 0.0.1

This library is under active development and no stable version was released yet.

If you like it a lot you may contribute by [financing](https://github.com/haijin-development/support-haijin-development) its development.

## Table of contents

1. [Installation](#c-1)
2. [Example](#c-2)
3. [Usage](#c-3)
    1. [Haiku sintax](#c-3-1)
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
        "haijin/haiku": "^0.0.1",
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
            = "Entrar al ciruelo"
            br
            = "en base a ternura"
            br
            = "en base a olfato."
        div.source data-id= "1", data-author= "Alberto Silva"
            = "Traducción de Alberto Silva - El libro del haiku"
```

<a name="c-3"></a>
## Usage

<a name="c-3-1"></a>
### Haiku sintax

<a name="c-3-1-1"></a>
#### Html tags

<a name="c-3-1-2"></a>
#### Tags attributes

<a name="c-3-1-3"></a>
#### Tags id and classes shortcuts

<a name="c-3-1-4"></a>
#### Text

<a name="c-3-1-5"></a>
#### PHP code evaluation

<a name="c-3-1-6"></a>
#### PHP code interpolation

<a name="c-3-1-7"></a>
#### Loops

<a name="c-3-1-8"></a>
#### Conditionals

<a name="c-3-1-9"></a>
#### Variables

<a name="c-3-2"></a>
### Rendering


<a name="c-4"></a>
## Running the specs

```
composer specs
```