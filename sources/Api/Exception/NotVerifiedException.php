<?php

namespace IPS\discord\Api\Exception;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Class NotVerifiedException
 *
 * @package IPS\discord\Api\Exception
 *
 * This exception is thrown when a member tries to link their discord account which is not verified.
 */
class _NotVerifiedException extends \RuntimeException
{
}
