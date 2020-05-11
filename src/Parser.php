<?php

namespace gendiff\Parser;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $pathToFile): array
{
    $fileExtension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    $fileContents = file_get_contents($pathToFile);

    switch ($fileExtension) {
        case "json":
            return json_decode($fileContents, true);
        case "yaml":
            return Yaml::parse($fileContents);
        default:
            break;
    }
}
