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
 * Class _AbstractModel
 *
 * @package IPS\discord\Model\Discord
 */
abstract class _AbstractModel
{
    /**
     * @var \Illuminate\Support\Collection $resourceCollection
     */
    protected $resourceCollection;

    /**
     * @param mixed $data Something that can be turned into a collection (array|Traversable etc.).
     */
    public function __construct($data)
    {
        $this->resourceCollection = collect($data);
    }

    /**
     * @see self::__construct();
     *
     * @param mixed $data
     *
     * @return static
     */
    public static function create($data)
    {
        return new static($data);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->resourceCollection;
    }

    /**
     * Prepare the data for use in a form. The data will be simplified to include
     * only the data IDs and their according names in the following format:
     *
     * @code
     * [
     *     0 => '',
     *     1 => 'Some name'
     * ]
     * @endcode
     *
     * @return \Illuminate\Support\Collection
     */
    public function prepareForForm()
    {
        return $this->resourceCollection->mapWithKeys(function ($resource) {
            if (is_array($resource) || $resource instanceof \ArrayAccess) {
                return [$resource['id'] => $resource['name']];
            }

            return [$resource->id => $resource->name];
        });
    }
}
