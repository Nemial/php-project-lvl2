<?php

namespace gendiff\Ast;

function makeNode($name, $type, $status, $deep, $path, $oldValue = null, $newValue = null, $children = null)
{
    return [
        "name" => $name,
        "type" => $type,
        "oldValue" => $oldValue,
        "newValue" => $newValue,
        "status" => $status,
        "deep" => $deep,
        "path" => $path,
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

function getStatus($node)
{
    return $node["status"];
}

function getDeep($node)
{
    return $node["deep"];
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

function getPath($node)
{
    return $node["path"];
}

function haveChildren($node)
{
    return getChildren($node) !== null;
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

function generateAst(object $before, object $after): array
{
    $iter = function ($before, $after, $deep, $path) use (&$iter) {
        $varsBefore = get_object_vars($before);
        $varsAfter = get_object_vars($after);
        $allVars = array_merge($varsBefore, $varsAfter);
        $allKeys = array_unique(array_keys($allVars));

        return array_reduce(
            $allKeys,
            function ($acc, $key) use ($varsBefore, $varsAfter, $deep, $path, $iter) {
                $haveAfterKey = array_key_exists($key, $varsAfter);
                $haveBeforeKey = array_key_exists($key, $varsBefore);
                $valueAfter = $haveAfterKey ? normalizeValue($varsAfter[$key]) : null;
                $valueBefore = $haveBeforeKey ? normalizeValue($varsBefore[$key]) : null;
                $newPath = $path === "" ? "{$key}" : "{$path}.{$key}";

                if ($haveAfterKey && $haveBeforeKey) {
                    if (is_object($varsAfter[$key]) && is_object($varsBefore[$key])) {
                        $newDeep = $deep + 2;
                        $acc[] = makeNode(
                            $key,
                            "node",
                            "unchanged",
                            $deep,
                            $newPath,
                            null,
                            null,
                            $iter($varsBefore[$key], $varsAfter[$key], $newDeep, $newPath),
                        );
                        return $acc;
                    }

                    if ($valueAfter !== $valueBefore) {
                        $acc[] = makeNode($key, "leaf", "changed", $deep, $newPath, $valueBefore, $valueAfter);
                        return $acc;
                    } else {
                        $acc[] = makeNode($key, "leaf", "unchanged", $deep, $newPath, $valueBefore);
                        return $acc;
                    }
                } elseif ($haveBeforeKey) {
                    if (is_object($varsBefore[$key])) {
                        $acc[] = makeNode($key, "node", "removed", $deep, $newPath, $valueBefore);
                        return $acc;
                    }
                    $acc[] = makeNode($key, "leaf", "removed", $deep, $newPath, $valueBefore);
                    return $acc;
                } elseif ($haveAfterKey) {
                    if (is_object($varsAfter[$key])) {
                        $acc[] = makeNode($key, "node", "added", $deep, $newPath, $valueAfter);
                        return $acc;
                    }
                    $acc[] = makeNode($key, "leaf", "added", $deep, $newPath, $valueAfter);
                    return $acc;
                }
            },
            []
        );
    };

    return $iter($before, $after, 1, "");
}
