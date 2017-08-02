<?php

/**
 * This file is part of Discord Integration.
 *
 * (c) Ahmad El-Bardan <ahmadelbardan@hotmail.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPS\discord\Model;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Class _Member
 *
 * @package IPS\discord\Model
 */
class _Member
{
    /**
     * @var \IPS\Member $member
     */
    protected $member;

    /**
     * @var \IPS\discord\Api\Guild $guild
     */
    protected $guild;

    /**
     * @param \IPS\Member $member
     */
    public function __construct( \IPS\Member $member )
    {
        $this->member = $member;
        $this->guild = \IPS\discord\Api\Guild::primary();
    }

    /**
     * Get the current roles for the given member.
     *
     * @return \Illuminate\Support\Collection
     */
    public function currentRoles()
    {
        $guildMember = $this->guild->getMember( (int)$this->member->discord_id );
        $guildRoles = $this->guild->roles()->collection();

        return collect( $guildMember->roles )
            ->flatMap(function ( $roleId ) use ( $guildRoles ) {
                return $guildRoles->where( 'id', $roleId );
            })
        ;
    }

    /**
     * Get all the roles that the user should have.
     *
     * @return \Illuminate\Support\Collection
     */
    public function shouldHaveRoles()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $memberShouldHaveGroups = collect( $this->member->discord_roles );

        /** @noinspection PhpUndefinedFieldInspection */
        if ( !\IPS\Settings::i()->discord_remove_unmapped )
        {
            $memberShouldHaveGroups = $memberShouldHaveGroups->merge(
                $this->getNotMappedRoles( $this->currentRoles() )
            );
        }

        return $memberShouldHaveGroups->filter()->unique()->values();
    }

    /**
     * Get roles that are not mapped.
     *
     * @param \Illuminate\Support\Collection $currentRoles
     *
     * @return \Illuminate\Support\Collection
     */
    public function getNotMappedRoles( \Illuminate\Support\Collection $currentRoles )
    {
        return $currentRoles->pluck( 'id' )->diff( $this->getMappedRoles() );
    }

    /**
     * Get all roles that are mapped.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getMappedRoles()
    {
        $mappedGroups = collect();

        $iterator = new \IPS\Patterns\ActiveRecordIterator(
            \IPS\Db::i()->select( '*', 'core_groups', ['discord_role != ? AND discord_role != ?', 0, ''] ),
            \IPS\Member\Group::class
        );

        /** @var \IPS\Member\Group $group */
        foreach ( $iterator as $group )
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $mappedGroups[] = $group->discord_role;
        }

        return $mappedGroups;
    }
}
