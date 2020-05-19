<?php

namespace gendiff\Core;

use gendiff\Formatters\Plain;
use gendiff\Formatters\Pretty;
use gendiff\Formatters\JSON;

use function gendiff\Ast\generateAst;
use function Funct\Collection\flattenAll;

function genDiff(object $firstFile, object $secondFile, string $format = "pretty"): string
{
    $ast = generateAst($firstFile, $secondFile);

    switch ($format) {
        case "pretty":
            $diffMap = Pretty\render($ast);
            array_unshift($diffMap, '{');
            array_push($diffMap, '}');
            break;
        case "plain":
            $diffMap = Plain\render($ast);
            break;
        case "json":
            $diffMap = JSON\render($ast);
            return json_encode($diffMap) . PHP_EOL;
        default:
            break;
    }
    $flatten = flattenAll($diffMap);

    return implode(PHP_EOL, $flatten) . PHP_EOL;
}
