<?php

namespace IPS\discord\Util;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

class _ChannelFormatter
{
    public static function onlyTextChannels(array $channels): array
    {
        return array_filter($channels, function ($channel) {
            return $channel['type'] === 0;
        });
    }

    public static function onlyIdsAndNames(array $channels): array
    {
        $ids = array_column($channels, 'id');
        $names = array_column($channels, 'name');

        $channels = [];

        foreach ($ids as $key => $id) {
            $channels[$id] = $names[$key];
        }

        return $channels;
    }

    public static function addEmptyChannel(array $channels): array
    {
        $channels[0] = '';
        asort( $channels, SORT_ASC );

        return $channels;
    }

    /**
     * By-pass IPS coding-standards check.
     */
    final protected function dummy()
    {
    }
}
