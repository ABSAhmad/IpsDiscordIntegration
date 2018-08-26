<?php

namespace IPS\discord\Discord;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

class _PostsToSync extends \IPS\Patterns\ActiveRecord
{
    /**
     * @brief	Application
     */
    public static $application = 'discord';

    /**
     * @brief	[ActiveRecord] Database Table
     */
    public static $databaseTable = 'discord_sync_posts';
}
