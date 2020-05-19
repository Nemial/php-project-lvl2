<?php

namespace gendiff\Formatters\Pretty;

use function gendiff\Ast\{
    getChildren,
    getMultiplier,
    getName,
    getOldValue,
    getNewValue,
    getType,
    getStatus,
    haveChildren
};

const COUNT_INDENT = 2;

function render(array $tree): array
{
    return array_reduce(
        $tree,
        function ($acc, $node) {
            $name = getName($node);
            $oldValue = getOldValue($node);
            if (getType($node) === "node") {
                $gap = str_repeat(" ", COUNT_INDENT * getMultiplier($node));
                switch (getStatus($node)) {
                    case "unchanged":
                        $acc[] = "  {$gap}{$name}: {";
                        if (haveChildren($node)) {
                            $acc[] = render(getChildren($node));
                        } else {
                            $key = array_key_first($oldValue);
                            $acc[] = "{$gap} {$key}: {$oldValue[$key]}";
                        }
                        $acc[] = "{$gap}  }";
                        break;
                    case "removed":
                        $acc[] = "{$gap}- {$name}: {";
                        $key = array_key_first($oldValue);
                        $acc[] = "{$gap}      {$key}: {$oldValue[$key]}";
                        $acc[] = "{$gap}  }";
                        break;
                    case "added":
                        $acc[] = "{$gap}+ {$name}: {";
                        $key = array_key_first($oldValue);
                        $acc[] = "{$gap}      {$key}: {$oldValue[$key]}";
                        $acc[] = "{$gap}  }";
                        break;
                    default:
                        break;
                }
            } elseif (getType($node) === "leaf") {
                $divisor = 2;
                $gap = str_repeat(" ", COUNT_INDENT * (getMultiplier($node) / $divisor));
                switch (getStatus($node)) {
                    case "unchanged":
                        $acc[] = "{$gap}     {$name}: {$oldValue}";
                        break;
                    case "changed":
                        $newValue = getNewValue($node);
                        $acc[] = "{$gap}   + {$name}: {$newValue}";
                        $acc[] = "{$gap}   - {$name}: {$oldValue}";
                        break;
                    case "removed":
                        $acc[] = "{$gap}   - {$name}: {$oldValue}";
                        break;
                    case "added":
                        $acc[] = "{$gap}   + {$name}: {$oldValue}";
                        break;
                    default:
                        break;
                }
            }
            return $acc;
        },
        []
    );
}
