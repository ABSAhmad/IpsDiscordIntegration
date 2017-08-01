//<?php

/**
 * This file is part of Discord Integration.
 *
 * (c) Ahmad El-Bardan <ahmadelbardan@hotmail.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    exit;
}

abstract class discord_hook_hookCssJs extends _HOOK_CLASS_
{
    public static function baseCss()
    {
        parent::baseCss();
        \IPS\Output::i()->cssFiles = array_merge(
            \IPS\Output::i()->cssFiles,
            \IPS\Theme::i()->css( 'login/discord.css', 'discord', 'global' )
        );
    }
}
