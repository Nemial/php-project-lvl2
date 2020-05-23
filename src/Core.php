<?php

namespace gendiff\Core;

use function gendiff\Ast\generateAst;
use function gendiff\File\{getFileContents, getFileExtension};
use function gendiff\Parser\parse;
use function gendiff\Formatters\makeDiffMap;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = "pretty"): string
{
    $beforeFileContents = getFileContents($pathToFile1);
    $beforeFileExtension = getFileExtension($pathToFile1);
    $afterFileContents = getFileContents($pathToFile2);
    $afterFileExtension = getFileExtension($pathToFile2);
    $before = parse($beforeFileContents, $beforeFileExtension);
    $after = parse($afterFileContents, $afterFileExtension);

    $ast = generateAst($before, $after);

    $diffMap = makeDiffMap($ast, $format);

    return $diffMap;
}
