<?php

namespace IPS\discord\setup\upg_10100;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * 1.1.0 Beta 1 Upgrade Code
 */
class _Upgrade
{
    /**
     *
     * @return	array	If returns TRUE, upgrader will proceed to next step. If it returns any other value, it will set this as the value of the 'extra' GET parameter and rerun this step (useful for loops)
     */
    public function step1()
    {
        \IPS\discord\Util::addAllAttributes();

        return TRUE;
    }
}
