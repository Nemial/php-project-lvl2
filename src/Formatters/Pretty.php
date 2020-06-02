<?php

namespace gendiff\Formatters\Pretty;

use function gendiff\Ast\{
    getChildren,
    getName,
    getOldValue,
    getNewValue,
    getType,
};

const COUNT_INDENT = 4;

function stringify($value, $deep)
{
    if (is_bool($value)) {
        return $value ? "true" : "false";
    }

    if (is_object($value)) {
        $currentValue = get_object_vars($value);
        $currentIndent = COUNT_INDENT * $deep;
        $indent = str_repeat(" ", $currentIndent);
        $valueIndentCount = $currentIndent + COUNT_INDENT;
        $valueIndent = str_repeat(" ", $valueIndentCount);
        $keys = array_keys($currentValue);
        $data = array_map(
            function ($key) use ($currentValue, $valueIndent) {
                $value = $currentValue[$key];
                return "{$valueIndent}{$key}: {$value}";
            },
            $keys
        );
        $imploded = implode("\n", $data);
        return "{\n{$imploded}\n{$indent}}";
    }

    return $value;
}

function render(array $tree): string
{
    $builded = build($tree);

    return "{\n{$builded}\n}";
}

function build($tree, $depth = 1): string
{
    return implode(
        "\n",
        array_map(
            function ($node) use ($depth) {
                $name = getName($node);
                $currentIndent = COUNT_INDENT * $depth;
                $indent = str_repeat(" ", $currentIndent);
                $shortIndentCount = (COUNT_INDENT * $depth) - 2;
                $shortIndent = str_repeat(" ", $shortIndentCount);
                $oldValue = stringify(getOldValue($node), $depth);
                $newValue = stringify(getNewValue($node), $depth);

                switch (getType($node)) {
                    case "object":
                        $newDepth = $depth + 1;
                        $children = build(getChildren($node), $newDepth);
                        return implode("\n", ["{$indent}{$name}: {", $children, "{$indent}}"]);
                    case "unchanged":
                        return "{$indent}{$name}: {$oldValue}";
                    case "changed":
                        return implode(
                            "\n",
                            ["{$shortIndent}+ {$name}: {$newValue}", "{$shortIndent}- {$name}: {$oldValue}"]
                        );
                    case "added":
                        return "{$shortIndent}+ {$name}: {$newValue}";
                    case "removed":
                        return "{$shortIndent}- {$name}: {$oldValue}";
                    default:
                        $type = getType($node);
                        throw new \Exception("Unsupported type node {$type}");
                }
            },
            $tree
        )
    );
}
