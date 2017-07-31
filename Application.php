<?php
/**
 * @brief		Discord Integration Application Class
 * @author		<a href=''>Ahmad E.</a>
 * @copyright	(c) 2017 Ahmad E.
 * @package		IPS Community Suite
 * @subpackage	Discord Integration
 * @since		01 Jan 2017
 */

namespace IPS\discord;

/**
 * Discord Integration Application Class
 * @TODO: Feature: Invite members to the discord server.
 * @TODO: Feature: Delay notifications.
 * @TODO: Feature: Bit.ly URL shortening?
 * @TODO: Discord Widget.
 *
 * @TODO: Feature: Pages support. Status: BLOCKED. Reason: \IPS\cms\modules\admin\databases::form() is not extendable.
 *
 * @TODO: Concept of notification settings.
 * @TODO: Feature: Notifications for PMs.
 * @TODO: Feature: Notifications for watched topics.
 * @TODO: (User)Setting: Send notifications on Discord?
 * @TODO: (User)Setting: Send notifications for approved posts.
 */

require_once __DIR__ . '/vendor/autoload.php';

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

        /**
         * Fix: "Permission too open" error.
         * Chmod files that need to be directly called to 644.
         * Because on some server configurations those are set to 666 by default and thus error out.
         */
        \chmod(
            \IPS\ROOT_PATH . '/applications/discord/interface/oauth/auth.php',
            \IPS\FILE_PERMISSION_NO_WRITE
        );

        if ( !$profileSync || !$systemLogin )
        {
            throw new \OutOfRangeException( 'Copying required file failed.' );
        }

        \IPS\discord\Util::addAllAttributes();
    }
}
