<?php

namespace Tests\Unit;

test('sum', function () {
    $res = 1 + 2;

    expect($res)->toBe(3);
});
