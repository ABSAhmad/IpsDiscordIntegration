<?php

namespace IPS\discord\Api;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Class _Guild
 *
 * @package IPS\discord\Api
 *
 * Handles any requests that are related to a discord guild.
 */
class _Guild extends \IPS\discord\Api
{
    /**
     * @var int $guildId
     */
    protected $guildId;

    /**
     * Sets up the connection to discord with the default guild.
     *
     * @param string|null $token
     *
     * @return static
     */
    public static function primary($token = null)
    {
        $guild = new static(compact('token'));

        return $guild->setGuildId( (int) \IPS\Settings::i()->discord_guild_id );
    }

    /**
     * Retrieves all channels for the given guild or the default one.
     *
     * @param int|null $guildId
     *
     * @return \Illuminate\Support\Collection
     */
    public function channels($guildId = null)
    {
        return collect($this->discord->guild->getGuildChannels([
            'guild.id' => (int) $guildId ?: $this->guildId
        ]))->map(function ($channel) {
            return (array) $channel;
        });
    }

    /**
     * Retrieves all text channels for the given guild or the default one.
     *
     * @param int|null $guildId
     *
     * @return \Illuminate\Support\Collection
     */
    public function textChannels($guildId = null)
    {
        return $this->channels($guildId)->reject(function (array $channel) {
            return $channel['type'] === \IPS\discord\Api\Channel::TYPE_VOICE;
        });
    }

    /**
     * Get the guild member for the given user id.
     *
     * @param int $userId
     * @param int|null $guildId
     *
     * @return \RestCord\Model\Guild\GuildMember
     */
    public function getMember($userId, $guildId = null)
    {
        return $this->discord->guild->getGuildMember([
            'user.id' => $userId,
            'guild.id' => (int) $guildId ?: $this->guildId
        ]);
    }

    /**
     * Ban the given user from the guild.
     *
     * @param int $userId
     * @param int|null $guildId
     *
     * @return \Illuminate\Support\Collection
     */
    public function banMember($userId, $guildId = null)
    {
        return collect($this->discord->guild->createGuildBan([
            'user.id' => $userId,
            'guild.id' => (int) $guildId ?: $this->guildId
        ]));
    }

    /**
     * Unban the given user from the guild.
     *
     * @param int $userId
     * @param int|null $guildId
     *
     * @return \Illuminate\Support\Collection
     */
    public function unbanMember($userId, $guildId = null)
    {
        return collect($this->discord->guild->removeGuildBan([
            'user.id' => $userId,
            'guild.id' => (int) $guildId ?: $this->guildId
        ]));
    }

    /**
     * Modify the given user with the given options.
     *
     * @param int $userId
     * @param array $options
     * @param int|null $guildId
     *
     * @return \Illuminate\Support\Collection
     */
    public function modifyMember($userId, array $options, $guildId = null)
    {
        $options = array_merge([
            'user.id' => $userId,
            'guild.id' => (int) $guildId ?: $this->guildId
        ], $options);

        return collect($this->discord->guild->modifyGuildMember($options));
    }

    /**
     * Remove the given user from the guild.
     *
     * @param int $userId
     * @param int|null $guildId
     *
     * @return \Illuminate\Support\Collection
     */
    public function removeMember($userId, $guildId = null)
    {
        return collect($this->discord->guild->removeGuildMember([
            'user.id' => $userId,
            'guild.id' => (int) $guildId ?: $this->guildId
        ]));
    }

    /**
     * Get all roles for the guild.
     *
     * @param int|null $guildId
     *
     * @return \Illuminate\Support\Collection
     */
    public function roles($guildId = null)
    {
        return collect($this->discord->guild->getGuildRoles([
            'guild.id' => $guildId ?: $this->guildId
        ]));
    }

    /**
     * Set the ID of the default guild that should be used as a fallback when there
     * is no other ID specified in a specific method call.
     *
     * @param int $guildId
     *
     * @return static
     */
    public function setGuildId($guildId)
    {
        $this->guildId = (int) $guildId;

        return $this;
    }

    /**
     * @return string
     */
    protected function getResourceName()
    {
        return 'guild';
    }
}
