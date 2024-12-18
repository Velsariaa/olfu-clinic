<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function el(string $tag, $attributes = null, $content = null) : string
{
    return \Spatie\HtmlElement\HtmlElement::render(...func_get_args());
}