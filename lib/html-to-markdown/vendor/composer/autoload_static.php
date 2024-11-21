<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5edeb8f758f7251e55a02ebb4ae999b6
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'League\\HTMLToMarkdown\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'League\\HTMLToMarkdown\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/html-to-markdown/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5edeb8f758f7251e55a02ebb4ae999b6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5edeb8f758f7251e55a02ebb4ae999b6::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5edeb8f758f7251e55a02ebb4ae999b6::$classMap;

        }, null, ClassLoader::class);
    }
}