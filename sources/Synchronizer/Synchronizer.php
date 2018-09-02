<?php

namespace IPS\discord;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

class _Synchronizer extends \IPS\Patterns\Singleton
{
    /**
     * @brief	Singleton Instances
     */
    protected static $instance;

    /** @var string[]|null */
    protected $allMappedDiscordRoles;

    public function syncMemberRoles(\IPS\Member $member, string $discordId)
    {
        $groupIds = $member->groups;

        $groups = array_map(function ($groupId) {
            return \IPS\Member\Group::load($groupId);
        }, $groupIds);

        if (\IPS\Settings::i()->discord_strict_group_sync) {
            $discordRoles = $this->buildDiscordRolesForStrictSyncing($groups);
        } else {
            $discordRoles = $this->buildDiscordRolesForNonStrictSyncing($discordId, $groups, $this->discordRoles());
        }

        \IPS\discord\Api\Guild\Member::i()->update(
            \IPS\Settings::i()->discord_guild_id,
            $discordId,
            [
                'roles' => $discordRoles
            ]
        );
    }

    protected function buildDiscordRolesForStrictSyncing(array $memberGroups): array
    {
        $discordRoles = array_map(function (\IPS\Member\Group $group) {
            return $group->discord_role;
        }, $memberGroups);

        /* Filter out potentially empty roles and return */
        return array_filter($discordRoles);
    }

    protected function buildDiscordRolesForNonStrictSyncing(string $discordId, array $memberGroups, array $allMappedDiscordRoles)
    {
        // Get current discord roles for the member
        $response = \IPS\discord\Api\Guild\Member::i()->get( \IPS\Settings::i()->discord_guild_id, $discordId );

        $currentRoles = $response['roles'] ?? [];

        if (empty($currentRoles)) {
            return $this->buildDiscordRolesForStrictSyncing($memberGroups);
        }

        $unmappedRoles = array_diff($currentRoles, $allMappedDiscordRoles);

        return array_merge($unmappedRoles, $this->buildDiscordRolesForStrictSyncing($memberGroups));
    }

    protected function discordRoles()
    {
        if ($this->allMappedDiscordRoles !== NULL) {
            return $this->allMappedDiscordRoles;
        }

        return $this->allMappedDiscordRoles = array_map(function (\IPS\Member\Group $group) {
            return $group->discord_role;
        }, \IPS\Member\Group::groups( TRUE, FALSE ));
    }
}
