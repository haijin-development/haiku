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
2. [Usage](#c-2)
3. [Running the specs](#c-3)

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
## Usage

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
## Running the specs

```
composer specs
```