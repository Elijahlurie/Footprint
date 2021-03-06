<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit411a0a9a24882b04c197e079a17ac331
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Twilio\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Twilio\\' => 
        array (
            0 => __DIR__ . '/..' . '/twilio/sdk/src/Twilio',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit411a0a9a24882b04c197e079a17ac331::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit411a0a9a24882b04c197e079a17ac331::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit411a0a9a24882b04c197e079a17ac331::$classMap;

        }, null, ClassLoader::class);
    }
}
