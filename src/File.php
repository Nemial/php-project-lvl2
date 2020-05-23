<?php

namespace gendiff\File;

function normalizePathToFile(string $pathToFile): string
{
    if (file_exists($pathToFile)) {
        return $pathToFile;
    }

    return realpath($pathToFile);
}

function getFileExtension(string $pathToFile): string
{
    return pathinfo(normalizePathToFile($pathToFile), PATHINFO_EXTENSION);
}

function getFileContents(string $file): string
{
    return file_get_contents(normalizePathToFile($file));
}
