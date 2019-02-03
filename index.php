#!/usr/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Codedungeon\PHPCliColors\Color;
use App\Cli;
use App\Files;
use App\Generator;

try {
    // execute the application
    (new Generator(new Cli($argv), new Files()))->exec();
} catch (\Exception $ex) {
    print Color::red() . $ex->getMessage() . Color::normal() . PHP_EOL;
}
