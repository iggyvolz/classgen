<?php

use iggyvolz\classgen\ClassGenerator;

require_once __DIR__ . "/src/ClassGenerator.php";

spl_autoload_register(function(string $class): void { ClassGenerator::autoload($class); }, prepend: true);
