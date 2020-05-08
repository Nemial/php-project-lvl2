<?php

namespace gendiff\Core;

function normalizePathToFile(string $pathToFile): string
{
    if (file_exists($pathToFile)) {
        return $pathToFile;
    }

    return getcwd() . DIRECTORY_SEPARATOR . $pathToFile;
}

function getFileContents(string $pathToFile): string
{
    return file_get_contents($pathToFile);
}

function decodeFileContents(string $file): array
{
    return json_decode($file, true);
}

function normalizeValue($value)
{
    if (is_bool($value)) {
        if ($value) {
            return 'true';
        }
        return 'false';
    }
    return $value;
}

function genDiff(array $firstFile, array $secondFile): string
{
    $keysFirstFile = array_keys($firstFile);
    $keysSecondFile = array_keys($secondFile);
    $allKeys = array_unique(array_merge($keysFirstFile, $keysSecondFile));

    $diffMap = array_reduce($allKeys, function ($acc, $key) use ($firstFile, $secondFile) {
        $haveFirstFileKey = array_key_exists($key, $firstFile);
        $haveSecondFileKey = array_key_exists($key, $secondFile);
        $firstValue = $haveFirstFileKey ? normalizeValue($firstFile[$key]) : null;
        $secondValue = $haveSecondFileKey ? normalizeValue($secondFile[$key]) : null;

        if ($haveFirstFileKey && $haveSecondFileKey) {
            if ($firstValue === $secondValue) {
                $acc[] = "     {$key}: {$firstValue}";
            } else {
                $acc[] = "   + {$key}: {$firstValue}";
                $acc[] = "   - {$key}: {$secondValue}";
            }
        } elseif (!$haveFirstFileKey) {
            $acc[] = "   + {$key}: {$secondValue}";
        } else {
            $acc[] = "   - {$key}: {$firstValue}";
        }

        return $acc;
    }, []);
    array_unshift($diffMap, '{');
    array_push($diffMap, '}');

    return implode(PHP_EOL, $diffMap) . PHP_EOL;
}
