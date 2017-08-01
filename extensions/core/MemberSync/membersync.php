<?php

/**
 * This file is part of Discord Integration.
 *
 * (c) Ahmad El-Bardan <ahmadelbardan@hotmail.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPS\discord\extensions\core\MemberSync;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined('\IPS\SUITE_UNIQUE_KEY') )
{
    header( (isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0') . ' 403 Forbidden' );
    exit;
}

/**
 * Member Sync
 */
class _membersync
{
    /**
     * This is triggered when a member account has been updated.
     *
     * @param  \IPS\Member $member  Member that is being updated.
     * @param  array $changes
     *
     * @return void
     */
    public function onProfileUpdate( \IPS\Member $member, array $changes )
    {
        if (!$this->hasGroupChanges($changes))
        {
            return;
        }

        try
        {
            $discordMemberUtil = new \IPS\discord\Util\Member(
                $this->addTheGroupChangesToTheMember( clone $member, $changes )
            );

            $guild = \IPS\discord\Api\Guild::primary();
            $guild->modifyMember(
                (int) $member->discord_id,
                ['roles' => $discordMemberUtil->shouldHaveRoles()->toArray()]
            );
        }
        catch ( \Exception $e )
        {
            /* Ignore, can be re-synced. */
            \IPS\Log::log( $e, 'discord_sync_group_on_profile_update' );
        }
    }

    /**
     * Triggered when a member is flagged as spammer.
     *
     * @param \IPS\Member $member
     *
     * @return void
     */
    public function onSetAsSpammer( \IPS\Member $member )
    {
        try
        {
            if ( \IPS\Settings::i()->discord_sync_bans )
            {
                \IPS\discord\Api\Guild::primary()->banMember( $member->discord_id );
            }
        }
        catch ( \Exception $e )
        {
            \IPS\Log::log( $e, 'discord_member_sync_on_set_as_spammer' );
        }
    }

    /**
     * Triggered when two members are being merged into one.
     *
     * @param \IPS\Member $member  The member that is being kept.
     * @param \IPS\Member $member2 The member that is being removed.
     *
     * @return void
     */
    public function onMerge( \IPS\Member $member, \IPS\Member $member2 )
    {
        try
        {
            \IPS\discord\Api\Guild::primary()->removeMember( $member2->discord_id );
        }
        catch ( \Exception $e )
        {
            \IPS\Log::log( $e, 'discord_member_sync_on_merge' );
        }
    }

    /**
     * Triggered when a member is deleted.
     *
     * @param \IPs\Member $member
     *
     * @return void
     */
    public function onDelete( \IPS\Member $member )
    {
        try
        {
            \IPS\discord\Api\Guild::primary()->removeMember( $member->discord_id );
        }
        catch ( \Exception $e )
        {
            \IPS\Log::log( $e, 'discord_member_sync_on_delete' );
        }
    }

    /**
     * @param array $changes
     *
     * @return bool
     */
    protected function hasGroupChanges(array $changes)
    {
        return isset($changes['member_group_id']) || isset($changes['mgroup_others']);
    }

    /**
     * Make sure to add the changes to the given member object, we need to do this because
     * the member does not have the changes assigned at this point, that happens later
     * in the IPS process, we do it on a copied object so that we do not interfere
     * with any of the IPS processing.
     *
     * @param \IPS\Member $member
     * @param array $changes
     *
     * @return \IPS\Member The modified member object that includes the correct groups.
     */
    protected function addTheGroupChangesToTheMember(\IPS\Member $member, array $changes)
    {
        if (isset($changes['member_group_id']))
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $member->member_group_id = (int)$changes['member_group_id'];
        }

        if (isset($changes['mgroup_others']))
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $member->mgroup_others = $changes['mgroup_others'];
        }

        return $member;
    }
}
