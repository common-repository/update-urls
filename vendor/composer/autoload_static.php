<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit213d4bd6c88328df21ce7e36c78e5f76
{
    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'KaizenCoders\\UpdateURLS\\PRO\\' => 28,
            'KaizenCoders\\UpdateURLS\\' => 24,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'KaizenCoders\\UpdateURLS\\PRO\\' => 
        array (
            0 => __DIR__ . '/../..' . '/pro/includes',
        ),
        'KaizenCoders\\UpdateURLS\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lite/includes',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit213d4bd6c88328df21ce7e36c78e5f76::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit213d4bd6c88328df21ce7e36c78e5f76::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit213d4bd6c88328df21ce7e36c78e5f76::$classMap;

        }, null, ClassLoader::class);
    }
}
