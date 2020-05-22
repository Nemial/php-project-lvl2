<?php

namespace gendiff\Core;

use gendiff\Formatters\Plain;
use gendiff\Formatters\Pretty;
use gendiff\Formatters\JSON;

use function gendiff\Ast\generateAst;
use function gendiff\Parser\parseFileContents;
use function Funct\Collection\flattenAll;

function normalizePathToFile(string $pathToFile): string
{
    if (file_exists($pathToFile)) {
        return $pathToFile;
    }

    return getcwd() . DIRECTORY_SEPARATOR . $pathToFile;
}

function getFileExtension(string $file): string
{
    return pathinfo(normalizePathToFile($file), PATHINFO_EXTENSION);
}

function getFileContents(string $file): string
{
    return file_get_contents(normalizePathToFile($file));
}

function makeDiffMap($ast, $format)
{
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

function genDiff(string $pathToFile1, string $pathToFile2, string $format = "pretty"): string
{
    $beforeFileContents = getFileContents($pathToFile1);
    $beforeFileExtension = getFileExtension($pathToFile1);
    $afterFileContents = getFileContents($pathToFile2);
    $afterFileExtension = getFileExtension($pathToFile2);
    $before = parseFileContents($beforeFileContents, $beforeFileExtension);
    $after = parseFileContents($afterFileContents, $afterFileExtension);

    $ast = generateAst($before, $after);

    $diffMap = makeDiffMap($ast, $format);

    return $diffMap;
}
