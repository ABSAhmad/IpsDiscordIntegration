<?php

namespace IPS\core\ProfileSync;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Discord Profile Sync
 */
class _Discord extends ProfileSyncAbstract
{
    /**
     * @brief Login handler key
     *
     * @var string $loginKey
     */
    public static $loginKey = 'Discord';

    /**
     * @var string $icon
     */
    public static $icon = 'lock';

    /**
     * @brief Authorization token
     *
     * @var string $authToken
     */
    protected $authToken;

    /**
     * @brief User data
     *
     * @var array $user
     */
    protected $user;

    /**
     * Get user data
     *
     * @return	array
     */
    protected function user()
    {
        if ( $this->user != NULL || !$this->member->discord_token )
        {
            return $this->user;
        }

        try
        {
            $response = \IPS\Http\Url::external( 'https://discordapp.com/api/v6/oauth2/token' )->request()->post([
                'client_id'		=> \IPS\Settings::i()->discord_client_id,
                'client_secret'	=> \IPS\Settings::i()->discord_client_secret,
                'refresh_token'	=> $this->member->discord_token,
                'grant_type'	=> 'refresh_token'
            ])->decodeJson();

            if ( isset( $response['access_token'] ) )
            {
                $this->authToken = $response['access_token'];
                $this->user = $this->get();
            }

            //TODO: helper method
            \IPS\discord\Api\Guild::primary()->modifyMember(
                $this->member->discord_id,
                [
                    'roles' => ( new \IPS\discord\Util\Member( $this->member ) )->shouldHaveRoles()->toArray()
                ]
            );
        }
        catch ( \IPS\discord\Api\Exception\NotVerifiedException $e )
        {
            $this->member->discord_token = NULL;
            $this->member->save();

            \IPS\Log::log( $e, 'discord_profile_sync_get' );

            \IPS\Output::i()->error( 'discord_not_verified', '' );
        }
        catch ( \Exception $e )
        {
            $this->member->discord_token = NULL;
            $this->member->save();

            \IPS\Log::log( $e, 'discord_profile_sync_get' );
        }

        return $this->user;
    }

    /**
     * @return bool
     */
    public function connected()
    {
        return $this->member->discord_id && $this->member->discord_token;
    }

    /**
     * Get photo
     *
     * @return \IPS\Http\Url|\IPS\File|NULL
     */
    public function photo()
    {
        try
        {
            $user = $this->user();

            if ( !isset( $user['avatar'] ) || empty( $user['avatar'] ) )
            {
                return NULL;
            }
        }
        catch ( \IPS\Http\Request\Exception $e )
        {
            \IPS\Log::log( $e, 'discord_get_photo' );

            return NULL;
        }

        return \IPS\Http\Url::external(
            "https://discordapp.com/api/v6/users/{$user['id']}/avatars/{$user['avatar']}.jpg"
        );
    }

    /**
     * @return string
     */
    public function name()
    {
        $user = $this->user();

        return isset( $user['username'] ) ? $user['username'] : '';
    }

    /**
     * @return void
     */
    protected function _disassociate()
    {
        $this->member->discord_id = 0;
        $this->member->discord_token = NULL;
        $this->member->save();
    }

    /**
     * Get API data
     *
     * @throws \IPS\discord\Api\Exception\NotVerifiedException
     *
     * @return \Illuminate\Support\Collection
     */
    protected function get()
    {
        $discordMember = \IPS\discord\Api\Member::create( $this->authToken )->getCurrent();

        if ( !$discordMember['verified'] )
        {
            throw new \IPS\discord\Api\Exception\NotVerifiedException();
        }

        return $discordMember;
    }
}
