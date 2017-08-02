<?php

/**
 * This file is part of Discord Integration.
 *
 * (c) Ahmad El-Bardan <ahmadelbardan@hotmail.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPS\discord\Model\Discord;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Class _Roles
 *
 * @package IPS\discord\Model\Discord
 */
class _Roles extends AbstractModel
{
    /**
     * Prepare the roles for use in a form. The roles will be simplified to include
     * only the role IDs and their according names. Also adds an empty role in the
     * beginning of the array in the following format:
     *
     * @code
     * [
     *     0 => '',
     *     1 => 'Name of the role'
     * ]
     * @endcode
     *
     * @return \Illuminate\Support\Collection
     */
    public function prepareForForm()
    {
        $this->resourceCollection = $this->resourceCollection->reject(function ($role) {
            if (is_array($role) || $role instanceof \ArrayAccess) {
                return $role['name'] == '@everyone' || $role['managed'];
            }

            return $role->name == '@everyone' || $role->managed;
        });

        return parent::prepareForForm()->put(0, '')->sort();
    }
}
