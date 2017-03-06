//<?php

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
