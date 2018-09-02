<?php
/**
 * @brief       Member Sync
 * @author      <a href='https://www.invisioncommunity.com'>Invision Power Services, Inc.</a>
 * @copyright   (c) Invision Power Services, Inc.
 * @license     https://www.invisioncommunity.com/legal/standards/
 * @package     Invision Community
 * @subpackage  Discord Integration
 * @since       27 Aug 2018
 */

namespace IPS\discord\extensions\core\MemberSync;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Member Sync
 */
class _SynchronizeMemberState
{
    /**
     * Member account has been updated
     *
     * @param   $member     \IPS\Member Member updating profile
     * @param   $changes    array       The changes
     * @return  void
     */
    public function onProfileUpdate( \IPS\Member $member, array $changes )
    {
        if (!\IPS\Settings::i()->discord_sync_groups) {
            return;
        }

        if ( !isset( $changes['member_group_id'] ) && !isset( $changes['mgroup_others'] ) ) {
            // When groups didn't change, just do nothing.
            return;
        }

        $count = \IPS\Db::i()->select(
            'token_identifier',
            ['core_login_links', 'login_link'],
            [
                'login_link.token_member = ? AND login_method.login_classname = ?', $member->member_id, \IPS\discord\LoginHandler\Discord::class
            ]
        )->join(
            ['core_login_methods', 'login_method'],
            'login_link.token_login_method = login_method.login_id'
        )->count();

        if ( $count === 0 )
        {
            return;
        }

        \IPS\Db::i()->insert('discord_sync_groups', [
            'member_id' => $member->member_id
        ]);
    }

    /**
     * Member is flagged as spammer
     *
     * @param   $member \IPS\Member The member
     * @return  void
     */
    public function onSetAsSpammer( \IPS\Member $member )
    {
    }

    /**
     * Member is unflagged as spammer
     *
     * @param   $member \IPS\Member The member
     * @return  void
     */
    public function onUnSetAsSpammer( \IPS\Member $member )
    {
    }

    /**
     * Member is merged with another member
     *
     * @param   \IPS\Member $memberToKeep
     * @param   \IPS\Member $memberToRemove
     * @return  void
     */
    public function onMerge( \IPS\Member $memberToKeep, \IPS\Member $memberToRemove )
    {
    }

    /**
     * Member is deleted
     *
     * @param   $member \IPS\Member The member
     * @return  void
     */
    public function onDelete( \IPS\Member $member )
    {
    }
}
