<?php

function balanceIndex($a) {
    $length = count($a);
    if ($length < 2) return 0;

    $half = array_sum($a) * 0.5;
    $count = $a[0];
    $index = 1;
    for ($i = 1; $i < $length - 1; $i++) {
        $count += $a[$i];
        if ($count >= $half) { // Sum is balanced
            $index = $i + 1;
            break;
        }
    }
    return $index;
}
