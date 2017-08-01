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

class discord_hook_downloadsApplication extends _HOOK_CLASS_
{
    /**
     * Install 'other' items. Left blank here so that application classes can override for app
     *  specific installation needs. Always run as the last step.
     *
     * @return void
     */
    public function installOther()
    {
        call_user_func_array( 'parent::installOther', func_get_args() );

        \IPS\discord\Util::addDownloadsAttributes();
    }
}
