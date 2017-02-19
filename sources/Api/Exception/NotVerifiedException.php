<?php

namespace IPS\discord\Api\Exception;

/**
 * Class NotVerifiedException
 *
 * @package IPS\discord\Api\Exception
 */
class _NotVerifiedException extends \IPS\discord\Api\Exception\BaseException
{
    /**
     * @var integer $httpErrorCode
     */
    public static $httpErrorCode = 0;
}
