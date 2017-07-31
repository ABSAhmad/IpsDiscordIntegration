<?php

namespace IPS\discord\Api;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Class _Member
 *
 * @package IPS\discord\Api
 *
 * Handles any requests that are related to a discord member.
 */
class _Member extends \IPS\discord\Api
{
    /**
     * Create an instance for the given token.
     *
     * @param string $token
     *
     * @return static
     */
    public static function create($token)
    {
        return new static([
            'token' => $token,
            'tokenType' => 'OAuth'
        ]);
    }

    /**
     * Retrieve the current user.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCurrent()
    {
        return collect($this->discord->user->getCurrentUser([]));
    }

    /**
     * @return string
     */
    protected function getResourceName()
    {
        return 'guild';
    }
}
