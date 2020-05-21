<?php

namespace gendiff\Core;

use gendiff\Formatters\Plain;
use gendiff\Formatters\Pretty;
use gendiff\Formatters\JSON;

use function gendiff\Ast\generateAst;
use function gendiff\Parser\{normalizePathToFile, parseFile};
use function Funct\Collection\flattenAll;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = "pretty"): string
{
    $before = parseFile(normalizePathToFile($pathToFile1));
    $after = parseFile(normalizePathToFile($pathToFile2));

    $ast = generateAst($before, $after);

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
