<?php

namespace IPS\discord\Util;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

class _MessageFormatter
{
    public static function createMessageFromIpsData(
        string $title,
        \IPS\Member $author,
        \IPS\Http\Url $url,
        string $format
    ) {
        return str_replace([
            '{title}',
            '{poster}',
            '{link}'
        ], [
            $title,
            $author->name,
            (string) $url
        ], $format);
    }

    protected function dummy()
    {
    }
}
