<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0417b054b42040010a9f07abc8b14766
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'BetValidator\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'BetValidator\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0417b054b42040010a9f07abc8b14766::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0417b054b42040010a9f07abc8b14766::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}