<?php


namespace IPS\discord\setup\upg_10008;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * 1.0.0 Beta 6 Upgrade Code
 */
class _Upgrade
{
    public function step1()
    {
        /**
         * Fix: "Permission too open" error.
         * Chmod files that need to be directly called to 644.
         * Because on some server configurations those are set to 666 by default and thus error out.
         */
        \chmod(
            \IPS\ROOT_PATH . '/applications/discord/interface/oauth/auth.php',
            \IPS\FILE_PERMISSION_NO_WRITE
        );

        /* Copy to /applications/core/sources/ProfileSync/ */
        $profileSync = \copy(
            \IPS\ROOT_PATH . '/applications/discord/sources/MoveOnInstall/ProfileSync/Discord.php',
            \IPS\ROOT_PATH . '/applications/core/sources/ProfileSync/Discord.php'
        );

        /* Copy to /system/Login/ */
        $systemLogin = \copy(
            \IPS\ROOT_PATH . '/applications/discord/sources/MoveOnInstall/Login/Discord.php',
            \IPS\ROOT_PATH . '/system/Login/Discord.php'
        );

        if ( !$profileSync || !$systemLogin )
        {
            throw new \OutOfRangeException( 'Copying required file failed.' );
        }

        return TRUE;
    }
}
