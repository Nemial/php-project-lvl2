<?php

namespace gendiff\Ast;

function makeNode($name, $type, $oldValue = null, $newValue = null, $children = null)
{
    return [
        "name" => $name,
        "type" => $type,
        "oldValue" => $oldValue,
        "newValue" => $newValue,
        "children" => $children
    ];
}

function getName($node)
{
    return $node["name"];
}

function getType($node)
{
    return $node["type"];
}

function getOldValue($node)
{
    return $node["oldValue"];
}

function getNewValue($node)
{
    return $node["newValue"];
}

function getChildren($node)
{
    return $node["children"];
}

function normalizeValue($value)
{
    if (is_bool($value)) {
        if ($value) {
            return 'true';
        } else {
            return 'false';
        }
    }

    if (is_object($value)) {
        return get_object_vars($value);
    }

    return $value;
}

function getUnionKeys($before, $after)
{
    $vars = array_merge($before, $after);

    return array_unique(array_keys($vars));
}

function generateAst(object $before, object $after): array
{
    $dataBefore = get_object_vars($before);
    $dataAfter = get_object_vars($after);
    $keys = getUnionKeys($dataBefore, $dataAfter);

    return array_map(
        function ($key) use ($dataBefore, $dataAfter) {
            $haveAfterKey = array_key_exists($key, $dataAfter);
            $haveBeforeKey = array_key_exists($key, $dataBefore);
            $valueAfter = $haveAfterKey ? normalizeValue($dataAfter[$key]) : null;
            $valueBefore = $haveBeforeKey ? normalizeValue($dataBefore[$key]) : null;

            if ($haveAfterKey && $haveBeforeKey) {
                if (is_object($dataAfter[$key]) && is_object($dataBefore[$key])) {
                    return makeNode(
                        $key,
                        "object",
                        null,
                        null,
                        generateAst($dataBefore[$key], $dataAfter[$key]),
                    );
                } elseif ($valueAfter !== $valueBefore) {
                    return makeNode($key, "changed", $valueBefore, $valueAfter);
                } else {
                    return makeNode($key, "unchanged", $valueBefore);
                }
            } elseif ($haveBeforeKey) {
                return makeNode($key, "removed", $valueBefore);
            } elseif ($haveAfterKey) {
                return makeNode($key, "added", $valueAfter);
            }
        },
        $keys
    );
}
