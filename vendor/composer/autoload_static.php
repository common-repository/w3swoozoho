<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit96a82a51853edbec4a140fee7f037dfc
{
    public static $files = array (
        '1d6289f15a91e4dfa7c24e0f413f2184' => __DIR__ . '/../..' . '/lib/class-tgm-plugin-activation.php',
    );

    public static $prefixLengthsPsr4 = array (
        'z' => 
        array (
            'zcrmsdk\\' => 8,
        ),
        'W' => 
        array (
            'W3SCloud\\WooZoho\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'zcrmsdk\\' => 
        array (
            0 => __DIR__ . '/..' . '/zohocrm/php-sdk-archive/src',
        ),
        'W3SCloud\\WooZoho\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'WeDevs_Settings_API' => __DIR__ . '/..' . '/tareq1988/wordpress-settings-api-class/src/class.settings-api.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit96a82a51853edbec4a140fee7f037dfc::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit96a82a51853edbec4a140fee7f037dfc::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit96a82a51853edbec4a140fee7f037dfc::$classMap;

        }, null, ClassLoader::class);
    }
}