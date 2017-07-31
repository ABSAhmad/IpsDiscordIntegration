<?php

namespace IPS\Login;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Class Discord
 *
 * @package \IPS\discord
 */
class _Discord extends LoginAbstract
{
    /**
     * @var string $icon
     */
    public static $icon = 'lock';

    /**
     * @param \IPS\Http\Url $url The URL for the login page
     * @param bool $ucp	 Is UCP (as opposed to login form)?
     * @param \IPS\Http\Url	$destination The URL to redirect to after a successful login.
     *
     * @return string
     */
    public function loginForm( \IPS\Http\Url $url, $ucp = FALSE, \IPS\Http\Url $destination = NULL )
    {
        return \IPS\Theme::i()
            ->getTemplate( 'login', 'discord', 'global' )
            ->discord(
                (string) $this->_discordSignInUrl(
                    ( $ucp ? 'ucp' : \IPS\Dispatcher::i()->controllerLocation ),
                    $destination
                )
            );
    }

    /**
     * @param string $url The URL for the login page.
     * @param \IPS\Member $member If we want to integrate this login method with an existing member, provide the member object.
     *
     * @throws \IPS\Login\Exception
     *
     * @return \IPS\Member
     */
    public function authenticate( $url, $member=NULL )
    {
        try
        {
            if ( \IPS\Request::i()->state !== \IPS\Session::i()->csrfKey )
            {
                throw new \IPS\Login\Exception( 'CSRF_FAIL', \IPS\Login\Exception::INTERNAL_ERROR );
            }

            /* Retrieve access token */
            $response = \IPS\Http\Url::external( 'https://discordapp.com/api/v6/oauth2/token' )
                ->request()
                ->post([
                    'client_id' => \IPS\Settings::i()->discord_client_id,
                    'client_secret' => \IPS\Settings::i()->discord_client_secret,
                    'grant_type' => 'authorization_code',
                    'redirect_uri'	=> (string) \IPS\Http\Url::internal( 'applications/discord/interface/oauth/auth.php', 'none' ),
                    'code' => \IPS\Request::i()->code
                ])
                ->decodeJson();

            if ( isset( $response['error'] ) || !isset( $response['access_token'] ) )
            {
                throw new \IPS\Login\Exception( 'generic_error', \IPS\Login\Exception::INTERNAL_ERROR );
            }

            /* Get user data */
            $discordMember = \IPS\discord\Api\Member::create( $response['access_token'] )->getCurrent();

            if ( !$discordMember['verified'] )
            {
                \IPS\Output::i()->error( 'discord_not_verified', '' );
            }

            /* Set member properties */
            $memberProperties = [
                'discord_id' => $discordMember['id'],
                'discord_token' => $response['access_token']
            ];

            if ( isset( $response['refresh_token'] ) )
            {
                $memberProperties['discord_token'] = $response['refresh_token'];
            }

            /* Find or create member */
            $member = $this->createOrUpdateAccount(
                $member ?: \IPS\Member::load( $discordMember['id'], 'discord_id' ),
                $memberProperties,
                $this->settings['real_name'] ? $discordMember['username'] : NULL,
                $discordMember['email'],
                $response['access_token'],
                array(
                    'photo' => TRUE,
                )
            );

            // TODO: helper method
            \IPS\discord\Api\Guild::primary()->modifyMember(
                $member->discord_id,
                [
                    'roles' => (new \IPS\discord\Util\Member( $member ))->shouldHaveRoles()->toArray()
                ]
            );

            /* Return */
            return $member;
        }
        catch ( \IPS\Http\Request\Exception $e )
        {
            throw new \IPS\Login\Exception( 'generic_error', \IPS\Login\Exception::INTERNAL_ERROR );
        }
    }

    /**
     * Link Account
     *
     * @param	\IPS\Member	$member		The member
     * @param	mixed		$details	Details as they were passed to the exception thrown in authenticate()
     * @return	void
     */
    public static function link( \IPS\Member $member, $details )
    {
        $userData = \IPS\discord\Api\Member::create( $details )->getCurrent();

        $member->discord_id = $userData['id'];
        $member->discord_token = $details;
        $member->save();

        // TODO: helper method
        \IPS\discord\Api\Guild::primary()->modifyMember(
            $member->discord_id,
            [
                'roles' => (new \IPS\discord\Util\Member( $member ))->shouldHaveRoles()->toArray()
            ]
        );
    }

    /**
     * ACP Settings Form
     *
     * @return	array	List of settings to save - settings will be stored to core_login_handlers.login_settings DB field
     * @code
    return array( 'savekey'	=> new \IPS\Helpers\Form\[Type]( ... ), ... );
     * @endcode
     */
    public function acpForm()
    {
        /* No config is needed here, all information is retrieved from the application settings. */
        return [];
    }

    /**
     * Test Settings
     *
     * @return	bool
     * @throws	\IPS\Http\Request\Exception
     * @throws	\UnexpectedValueException	If response code is not 302
     */
    public function testSettings()
    {
        return TRUE;
    }

    /**
     * Can a member sign in with this login handler?
     * Used to ensure when a user disassociates a social login that they have some other way of logging in
     *
     * @param	\IPS\Member	$member	The member
     * @return	bool
     */
    public function canProcess( \IPS\Member $member )
    {
        return ( $member->discord_id && $member->discord_token );
    }

    /**
     * Get sign in URL
     *
     * @param	string			$base			Controls where the user is taken back to
     * @param	\IPS\Http\Url	$destination	The URL to redirect to after a successful login
     *
     * @return	\IPS\Http\Url
     */
    protected function _discordSignInUrl( $base, \IPS\Http\Url $destination = NULL )
    {
        $params = [
            'response_type'	=> 'code',
            'client_id' => \IPS\Settings::i()->discord_client_id,
            'redirect_uri'	=> (string) \IPS\Http\Url::internal( 'applications/discord/interface/oauth/auth.php', 'none' ),
            'scope' => 'email identify',
            'state' => $base . '-' . \IPS\Session::i()->csrfKey . '-' . ( $destination ? base64_encode( $destination ) : '' )
        ];

        return \IPS\Http\Url::external( 'https://discordapp.com/api/v6/oauth2/authorize' )->setQueryString( $params );
    }

    /**
     * Can a member change their email/password with this login handler?
     *
     * @param	string		$type	'email' or 'password'
     * @param	\IPS\Member	$member	The member
     * @return	bool
     */
    public function canChange( $type, \IPS\Member $member )
    {
        return FALSE;
    }
}
