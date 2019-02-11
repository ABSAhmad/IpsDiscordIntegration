<?php

namespace IPS\discord\modules\admin\settings;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Discord settings
 */
class _settings extends \IPS\Dispatcher\Controller
{
    /** @var \IPS\Settings */
    protected $settings;

    public function __construct(\IPS\Http\Url $url = null)
    {
        parent::__construct($url);

        $this->settings = \IPS\Settings::i();
    }

    /**
     * Execute
     *
     * @return  void
     */
    public function execute()
    {
        \IPS\Dispatcher::i()->checkAcpPermission( 'settings_manage' );
        parent::execute();

        \IPS\Output::i()->jsFiles = array_merge(
            \IPS\Output::i()->jsFiles,
            \IPS\Output::i()->js( 'admin_settings.js', 'discord', 'admin' )
        );
    }

    /**
     * Show settings form.
     *
     * @return  void
     */
    protected function manage()
    {
        $settings = \IPS\Settings::i();
        $redirectUris = [
            (string) \IPS\Http\Url::internal( 'app=discord&module=register&controller=link&do=admin', 'front' ),
            (string) \IPS\Http\Url::internal( 'applications/discord/interface/oauth/auth.php', 'none' )
        ];

        $form = new \IPS\Helpers\Form;

        if ( $settings->discord_bot_token )
        {
            $form->addButton( 'discord_handshake', 'button', NULL, 'ipsButton ipsButton_alternate', [
                'data-controller' => "discord.admin.settings.handshake",
                'data-token' => $settings->discord_bot_token
            ] );
        }

        $form->addTab( 'discord_connection_settings' );
        $form->addMessage(
            \IPS\Member::loggedIn()->language()->addToStack( 'discord_redirect_uris', FALSE, [
                'sprintf' => $redirectUris
            ]),
            'ipsMessage ipsMessage_info'
        );
        $form->add(
            new \IPS\Helpers\Form\Text( 'discord_client_id', $settings->discord_client_id ?: NULL, TRUE )
        );
        $form->add(
            new \IPS\Helpers\Form\Password( 'discord_client_secret', $settings->discord_client_secret ?: NULL, TRUE )
        );
        $form->add(
            new \IPS\Helpers\Form\Password( 'discord_bot_token', $settings->discord_bot_token ?: NULL, TRUE )
        );
        $form->add(
            new \IPS\Helpers\Form\Text( 'discord_guild_id', $settings->discord_guild_id ?: NULL, FALSE )
        );

        $form->addTab( 'discord_map_settings' );

        $form->add(
            new \IPS\Helpers\Form\YesNo( 'discord_sync_groups', $settings->discord_sync_groups ?: TRUE, FALSE, [ 'togglesOn' => [ 'discord_strict_group_sync' ] ] )
        );

        $form->add(
            new \IPS\Helpers\Form\YesNo( 'discord_strict_group_sync', $settings->discord_strict_group_sync ?: FALSE, FALSE, [], NULL, NULL, NULL, 'discord_strict_group_sync' )
        );

        $form->add(
            new \IPS\Helpers\Form\Radio('discord_on_set_as_spammer', NULL, FALSE, ['options' => [
                'ban' => 'ban_member',
                'kick' => 'kick_member',
                'nothing' => 'not_a_thing'
            ]])
        );

        $form->add(
            new \IPS\Helpers\Form\YesNo( 'discord_sync_bans', $settings->discord_sync_bans ?: FALSE )
        );
        $form->add(
            new \IPS\Helpers\Form\YesNo( 'discord_sync_names', $settings->discord_sync_names ?: FALSE )
        );

        if ($settings->discord_guild_id && \IPS\Application::appIsEnabled('forums')) {
            $form = $this->buildForumForm($form);

            $form->add(
                new \IPS\Helpers\Form\TextArea( 'discord_approved_post_format', $settings->discord_approved_post_format ?: NULL )
            );

            $form->add(
                new \IPS\Helpers\Form\TextArea( 'discord_unapproved_post_format', $settings->discord_unapproved_post_format ?: NULL )
            );
        }

        if ( $values = $form->values() )
        {
            if ( empty( $settings->discord_guild_id ) || empty( $values['discord_guild_id'] ) )
            {
                $redirect = \IPS\Http\Url::external( \IPS\discord\Api\Client::OAUTH2_URL . 'authorize' )
                    ->setQueryString([
                        'client_id' => $values['discord_client_id'],
                        'permissions' => \IPS\discord\Api\Client::PERM_ADMINISTRATOR,
                        'response_type' => 'code',
                        'scope' => \IPS\discord\Api\Client::SCOPE_BOT,
                        'redirect_uri' => $redirectUris[0]
                    ]);
            }
            else
            {
                $redirect = \IPS\Http\Url::internal( 'app=discord&module=settings&controller=settings' );
            }

            $form->saveAsSettings( $this->formatFormValues($values) );

            \IPS\Output::i()->redirect( $redirect );
        }

        /* Output */
        \IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack( 'discord_setting_title' );
        \IPS\Output::i()->output = (string) $form;
    }

    protected function buildForumForm(\IPS\Helpers\Form $form)
    {
        $channels = \IPS\discord\Api\Guild\Channel::i()->all($this->settings->discord_guild_id);

        $form->addTab( 'discord_forums_settings' );
        $form->addHeader('discord_post_approved');

        foreach ($channels as $channel) {
            if ($channel['type'] != 0) { continue; }

            $id = "discord_posts_approved_{$channel['id']}";

            $s = json_decode($this->settings->discord_approved_posts_from_forums, TRUE);
            $defaultValue = $s[$channel['id']] ?? null;

            $node = new \IPS\Helpers\Form\Node( $id, $defaultValue, FALSE, [
                'url'                   => \IPS\Http\Url::internal( 'app=discord&module=settings&controller=settings' ),
                'class'                 => \IPS\forums\Forum::class,
                'multiple' => TRUE,
                'zeroVal' => 'none',
                NULL,
                NULL,
                NULL,
                $id
            ]);

            $node->label = $channel['name'];
            $form->add( $node );
        }

        $form->addHeader('discord_post_unapproved');

        foreach ($channels as $channel) {
            if ($channel['type'] != 0) { continue; }

            $id = "discord_posts_unapproved_{$channel['id']}";

            $s = json_decode($this->settings->discord_unapproved_posts_from_forums, TRUE);
            $defaultValue = $s[$channel['id']] ?? null;

            $node = new \IPS\Helpers\Form\Node( $id, $defaultValue, FALSE, [
                'url'                   => \IPS\Http\Url::internal( 'app=discord&module=settings&controller=settings' ),
                'class'                 => \IPS\forums\Forum::class,
                'multiple' => TRUE,
                'zeroVal' => 'none',
                NULL,
                NULL,
                NULL,
                $id
            ]);

            $node->label = $channel['name'];
            $form->add( $node );
        }

        return $form;
    }

    protected function formatFormValues(array $values): array
    {
        $approvedList = 'discord_approved_posts_from_forums';
        $unapprovedList = 'discord_unapproved_posts_from_forums';

        foreach ($values as $key => $value)
        {
            $channelId = mb_substr($key, mb_strrpos($key, '_') + 1);

            if (mb_strpos($key, '_posts_approved_') !== FALSE) {
                $newArrayKey = $approvedList;
            }
            elseif (mb_strpos($key, '_posts_unapproved_') !== FALSE) {
                $newArrayKey = $unapprovedList;
            }
            else
            {
                continue;
            }

            if (is_array($value))
            {
                $value = array_map(function ($forum) {
                    return $forum->id;
                }, $value);
            }

            $$newArrayKey[$channelId] = $value;
            unset($values[$key]);
        }

        $values[$approvedList] = json_encode($$approvedList);
        $values[$unapprovedList] = json_encode($$unapprovedList);

        return $values;
    }
}
