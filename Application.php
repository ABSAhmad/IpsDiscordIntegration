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

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

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
     * Make sure we add our needed columns.
     */
    public function installOther()
    {
        \IPS\discord\Util::addAllAttributes();
    }
}
