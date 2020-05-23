<?php

namespace gendiff\Formatters\JSON;

use function gendiff\Ast\{
    getChildren,
    getName,
    getType,
};

function render(array $tree): string
{
    $iter = function ($tree) use (&$iter) {
        return array_reduce(
            $tree,
            function ($acc, $node) use ($iter) {
                $name = getName($node);
                if (getType($node) === "object") {
                    $acc[$name] = $iter(getChildren($node));
                    return $acc;
                }
                switch (getType($node)) {
                    case "changed":
                        $acc[$name] = "changed";
                        break;
                    case "removed":
                        $acc[$name] = "removed";
                        break;
                    case "added":
                        $acc[$name] = "added";
                        break;
                    default:
                        break;
                }
                return $acc;
            },
            []
        );
    };

    $rendered = $iter($tree);
    $formatted = json_encode($rendered) . "\n";

    return $formatted;
}
