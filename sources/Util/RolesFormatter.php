<?php

namespace IPS\discord\Util;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

class _RolesFormatter
{
    public static function onlyNormalGroups(array $roles): array
    {
        return array_filter($roles, function ($role) {
            return $role['name'] !== '@everyone' && $role['managed'] !== TRUE;
        });
    }

    public static function onlyIdsAndNames(array $roles): array
    {
        $ids = array_column($roles, 'id');
        $names = array_column($roles, 'name');

        $roles = [];

        foreach ($ids as $key => $id) {
            $roles[$id] = $names[$key];
        }

        return $roles;
    }

    public static function addEmptyRole(array $roles): array
    {
        $roles[0] = '';
        asort( $roles, SORT_ASC );

        return $roles;
    }

    /**
     * By-pass IPS coding-standards check.
     */
    final protected function dummy()
    {
    }
}
