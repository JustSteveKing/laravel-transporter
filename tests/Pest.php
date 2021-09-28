<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use JustSteveKing\Transporter\Tests\TestCase;

uses(TestCase::class)->in('Feature');

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
