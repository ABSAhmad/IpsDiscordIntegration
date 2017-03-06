<?php

namespace IPS\discord;

/**
 * Class Util
 *
 * @package IPS\discord
 */
class _Util
{
    /**
     * Add our needed columns to all tables.
     */
    public static function addAllAttributes()
    {
        if ( static::appIsInstalled( 'downloads' ) )
        {
            static::addDownloadsAttributes();
        }

        if ( static::appIsInstalled( 'calendar' ) )
        {
            static::addCalendarAttributes();
        }

        if ( static::appIsInstalled( 'forums' ) )
        {
            static::addForumsAttributes();
        }
    }

    /**
     * Add our needed columns to the calendar tables.
     *
     * @return void
     */
    public static function addCalendarAttributes()
    {
        \IPS\Db::i()->addColumn( 'calendar_calendars', [
            'name' => 'cal_discord_channel_approved',
            'type' => 'VARCHAR',
            'length' => 64,
            'default' => '0',
        ]);

        \IPS\Db::i()->addColumn( 'calendar_calendars', [
            'name' => 'cal_discord_channel_unapproved',
            'type' => 'VARCHAR',
            'length' => 64,
            'default' => '0',
        ]);

        \IPS\Db::i()->addColumn( 'calendar_calendars', [
            'name' => 'cal_discord_post_format',
            'type' => 'TEXT',
            'default' => NULL,
        ]);
    }

    /**
     * Add our needed columns to the downloads tables.
     *
     * @return void
     */
    public static function addDownloadsAttributes()
    {
        \IPS\Db::i()->addColumn( 'downloads_categories', [
            'name' => 'cdiscord_channel_approved',
            'type' => 'VARCHAR',
            'length' => 64,
            'default' => '0',
        ]);

        \IPS\Db::i()->addColumn( 'downloads_categories', [
            'name' => 'cdiscord_channel_unapproved',
            'type' => 'VARCHAR',
            'length' => 64,
            'default' => '0',
        ]);

        \IPS\Db::i()->addColumn( 'downloads_categories', [
            'name' => 'cdiscord_post_format',
            'type' => 'TEXT',
            'default' => NULL,
        ]);
    }

    /**
     * Add our needed columns to the forums tables.
     *
     * @return void
     */
    public static function addForumsAttributes()
    {
        \IPS\Db::i()->addColumn( 'forums_forums', [
            'name' => 'discord_post_format',
            'type' => 'TEXT',
            'default' => NULL,
        ] );

        \IPS\Db::i()->addColumn( 'forums_forums', [
            'name' => 'discord_topic_format',
            'type' => 'TEXT',
            'default' => NULL,
        ]);

        \IPS\Db::i()->addColumn( 'forums_forums', [
            'name' => 'discord_post_topics',
            'type' => 'TINYINT',
            'length' => 1,
            'default' => 0,
        ]);

        \IPS\Db::i()->addColumn( 'forums_forums', [
            'name' => 'discord_post_unapproved_topics',
            'type' => 'TINYINT',
            'length' => 1,
            'default' => 0,
        ]);

        \IPS\Db::i()->addColumn( 'forums_forums', [
            'name' => 'discord_post_posts',
            'type' => 'TINYINT',
            'length' => 1,
            'default' => 0,
        ]);

        \IPS\Db::i()->addColumn( 'forums_forums', [
            'name' => 'discord_post_unapproved_posts',
            'type' => 'TINYINT',
            'length' => 1,
            'default' => 0,
        ]);
    }

    /**
     * Check if app is installed.
     *
     * @param string $name
     * @return bool
     */
    protected static function appIsInstalled( $name )
    {
        return array_key_exists( $name, \IPS\Application::applications() );
    }

    /**
     * By-pass IPS coding-standards check.
     */
    final protected function dummy()
    {}
}
