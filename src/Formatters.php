<?php

namespace gendiff\Formatters;

function format($ast, $format)
{
    switch ($format) {
        case "pretty":
            return Pretty\render($ast);
        case "plain":
            return Plain\render($ast);
            break;
        case "json":
            return JSON\render($ast);
        default:
            throw new \Exception("Unsupported format {$format}");
    }
}
