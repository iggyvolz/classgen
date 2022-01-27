<?php
declare(strict_types=1);

namespace iggyvolz\classgen;

use Stringable;

abstract class ClassGenerator
{
    public final function __construct(){}

    /**
     * @var array<string,ClassGenerator>
     */
    private static array $autoloaders = [];

    /**
     * Regenerate classes every time
     */
    public const MODE_DEBUG = 1;

    /**
     * If a class exists, leave it alone
     */
    public const MODE_PRODUCTION = 2;

    /**
     * eval() classes instead of writing them to disk
     */
    public const MODE_RUNTIME = 3;

    private static int $mode = self::MODE_DEBUG;

    public static function setMode(int $mode):void
    {
        self::$mode = $mode;
    }

    public final static function autoload(string $class):void
    {
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . "gen" . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
        if(file_exists($file) && self::$mode !== self::MODE_DEBUG) {
            // File already exists, do not write it
            return;
        }
        foreach(self::$autoloaders as $generator) {
            if($generator->isValid($class)) {
                $conts =$generator->generate($class);
                if(self::$mode === self::MODE_RUNTIME) {
                    eval($conts);
                    return;
                } else {
                    if(!is_dir(dirname($file))) {
                        mkdir(dirname($file), recursive: true);
                    }
                    file_put_contents($file, $conts);
                    return;
                }
            }
        }
    }

    protected abstract function isValid(string $class): bool;

    protected abstract function generate(string $class): string|Stringable;
    
    public static function register(mixed ...$args): void
    {
        if(!array_key_exists(static::class, self::$autoloaders)) {
            self::$autoloaders[static::class] = new static(...$args);
        }
    }
}
