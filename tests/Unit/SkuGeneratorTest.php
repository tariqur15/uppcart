<?php

use App\Services\SkuGenerator;

it('generates sku from a normal multi-word title', function () {
    $sku = SkuGenerator::generate("Men's Cotton Casual Shirt");
    $date = date('dmy');

    expect($sku)->toBe("MEN-COT-CAS-{$date}");
});

it('pads short words with X', function () {
    $sku = SkuGenerator::generate('I Love PHP');
    $date = date('dmy');

    expect($sku)->toBe("IXX-LOV-PHP-{$date}");
});

it('uses GEN placeholder when fewer than 3 words', function () {
    $sku = SkuGenerator::generate('Red Tea');
    $date = date('dmy');

    expect($sku)->toBe("RED-TEA-GEN-{$date}");
});

it('uses GEN placeholder for a single word', function () {
    $sku = SkuGenerator::generate('Hello');
    $date = date('dmy');

    expect($sku)->toBe("HEL-GEN-GEN-{$date}");
});

it('strips special characters before processing', function () {
    $sku = SkuGenerator::generate("Men's Cotton Casual Shirt!");
    $date = date('dmy');

    expect($sku)->toBe("MEN-COT-CAS-{$date}");
});

it('pads a 1-character word with Xs', function () {
    $sku = SkuGenerator::generate('A T');
    $date = date('dmy');

    expect($sku)->toBe("AXX-TXX-GEN-{$date}");
});

it('pads a 2-character word with X', function () {
    $sku = SkuGenerator::generate('Go Run Fast');
    $date = date('dmy');

    expect($sku)->toBe("GOX-RUN-FAS-{$date}");
});

it('handles a title with exactly 3 words', function () {
    $sku = SkuGenerator::generate('Big Red Ball');
    $date = date('dmy');

    expect($sku)->toBe("BIG-RED-BAL-{$date}");
});

it('uses only the first 3 words and ignores extras', function () {
    $sku = SkuGenerator::generate('The Quick Brown Fox Jumps');
    $date = date('dmy');

    expect($sku)->toBe("THE-QUI-BRO-{$date}");
});

it('returns the correct date format DDMMYY', function () {
    $sku = SkuGenerator::generate('Hello World Test');

    expect($sku)->toEndWith(date('dmy'));
});

it('format follows WORD-WORD-WORD-DDMMYY pattern', function () {
    $sku = SkuGenerator::generate('Test Product Name');

    expect($sku)->toMatch('/^[A-Z]{3}-[A-Z]{3}-[A-Z]{3}-\d{6}$/');
});

it('handles all lowercase input', function () {
    $sku = SkuGenerator::generate('cotton shirt sale');
    $date = date('dmy');

    expect($sku)->toBe("COT-SHI-SAL-{$date}");
});

it('handles mixed case input', function () {
    $sku = SkuGenerator::generate('cOtToN sHiRt SaLe');
    $date = date('dmy');

    expect($sku)->toBe("COT-SHI-SAL-{$date}");
});

it('strips numbers from the title', function () {
    $sku = SkuGenerator::generate('Size 12 Shirt');
    $date = date('dmy');

    expect($sku)->toBe("SIZ-SHI-GEN-{$date}");
});

it('handles title with only special characters and spaces', function () {
    $sku = SkuGenerator::generate('!!! @@@ ###');

    expect($sku)->toBe('GEN-GEN-GEN-'.date('dmy'));
});
