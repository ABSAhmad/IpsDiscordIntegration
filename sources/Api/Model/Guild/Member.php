<?php

namespace IPS\discord\Api\Model\Guild;

class _Member
{
    /** @var User */
    public $user;

    /** @var string|null */
    public $nick;

    /** @var string[] */
    public $roles;

    /** @var string */
    public $joinedAt;

    /** @var bool */
    public $deaf;

    /** @var bool */
    public $mute;
}
