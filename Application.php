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
class _Application extends \IPS\Application
{
    /**
     * Make sure we have our login handler in the correct table.
     * Make sure we move our login handler files.
     * Make sure we add our needed columns.
     */
    public function installOther()
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

        \IPS\discord\Util::addAllAttributes();
    }
}
