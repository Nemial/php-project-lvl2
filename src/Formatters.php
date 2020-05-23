<?php

namespace gendiff\Formatters;

function makeDiffMap($ast, $format)
{
    switch ($format) {
        case "pretty":
            $diffMap = Pretty\render($ast);
            break;
        case "plain":
            $diffMap = Plain\render($ast);
            break;
        case "json":
            $diffMap = JSON\render($ast);
            break;
        default:
            throw new \Exception("Undefined format");
    }

    return $diffMap;
}
