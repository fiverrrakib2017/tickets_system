<?php

class Console
{
    public static function info(string $message): void
    {
        self::write("INFO", $message, "\033[36m");
    }

    public static function success(string $message): void
    {
        /*----------Green---------*/
        self::write("SUCCESS", $message, "\033[32m");
    }

    public static function error(string $message): void
    {
        /*----------Red---------*/
        self::write("ERROR", $message, "\033[31m"); 
    }

    public static function warn(string $message): void
    {
        /*----------Yellow---------*/
        self::write("WARN", $message, "\033[33m");
    }

    private static function write(string $type, string $message, string $color): void
    {
        $reset = "\033[0m";

        echo $color
            . "[{$type}] "
            . $message
            . $reset
            . PHP_EOL;
    }
}