<?php

/**
 * This file is part of Discord Integration.
 *
 * (c) Ahmad El-Bardan <ahmadelbardan@hotmail.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPS\discord;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Discord Integration Application Class
 */
class _Application extends \IPS\Application
{
    /**
     * Make sure we have our login handler in the correct table.
     * Make sure we move our login handler files.
     * Make sure we add our needed columns.
     */
    public function installOther()
    {
        $maxLoginOrder = \IPS\Db::i()->select( 'MAX(login_order)', 'core_login_handlers' )->first();

        \IPS\Db::i()->insert('core_login_handlers', [
            'login_settings' => '',
            'login_key' => 'Discord',
            'login_enabled' => 1,
            'login_order' => $maxLoginOrder + 1,
            'login_acp' => 0
        ]);

        \IPS\discord\Util::updateLoginHandlerFiles();

        /**
         * Fix: "Permission too open" error.
         * Chmod files that need to be directly called to 644.
         * Because on some server configurations those are set to 666 by default and thus error out.
         */
        \chmod(
            \IPS\ROOT_PATH . '/applications/discord/interface/oauth/auth.php',
            \IPS\FILE_PERMISSION_NO_WRITE
        );

        \IPS\discord\Util::addAllAttributes();
    }
}
