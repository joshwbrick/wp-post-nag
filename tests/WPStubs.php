<?php

$wpdb = null;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Brickner\WPPostNag\Plugin;

class WPStubs extends MockeryTestCase
{
    public static function stub($member)
    {
        $methodStub =  str_replace(' ', '', ucwords(str_replace('_', ' ', $member)));
        $method = "stub$methodStub";

        self::$method();
    }

    protected static function stubAddAction()
    {
        if ( ! function_exists('add_action')) {
            function add_action()
            {
            }
        }
    }

    protected static function stubRegisterSetting()
    {
        if ( ! function_exists('register_setting')) {
            function register_setting()
            {
            }
        }
    }

    protected static function stubAddSettingsField()
    {
        if ( ! function_exists('add_settings_field')) {
            function add_settings_field()
            {
            }
        }
    }

    protected static function stubAddSettingsSection()
    {
        if ( ! function_exists('add_settings_section')) {
            function add_settings_section()
            {
            }
        }
    }

    protected static function stubGetOption()
    {
        if ( ! function_exists('get_option')) {
            function get_option($id, $default)
            {
                switch ($id) {
                    case Plugin::SETTINGS_PAT_ID:
                        $value = Plugin::SETTINGS_PAT_DAYS_DEFAULT;
                        break;
                    case Plugin::SETTINGS_IMPAT_ID:
                        $value = Plugin::SETTINGS_IMPAT_DAYS_DEFAULT;
                        break;
                    default:
                        $value = 'option_value';
                        break;
                }

                return $value;
            }
        }
    }

    protected static function stubWpdb()
    {
        global $wpdb;

        $wpdb = Mockery::mock();
        $wpdb->shouldReceive('prepare');
        $wpdb->allows('posts');
        $wpdb->shouldReceive('get_var')
             ->set('posts', ['wp_posts'])
             ->andReturn(0);
    }

    protected static function stubWpdbDefaultNag()
    {
        global $wpdb;

        $wpdb = Mockery::mock();
        $wpdb->shouldReceive('prepare');
        $wpdb->allows('posts');
        $wpdb->shouldReceive('get_var')
             ->set('posts', ['wp_posts'])
             ->andReturn(date('Y-m-d H:i:s'));
    }

    protected static function stubWpdbPatNag()
    {
        global $wpdb;

        $time = Plugin::SECONDS_IN_DAY * (Plugin::SETTINGS_PAT_DAYS_DEFAULT + 1);
        $date = date('Y-m-d H:i:s', time() - $time);
        $wpdb = Mockery::mock();
        $wpdb->shouldReceive('prepare');
        $wpdb->shouldReceive('get_var')
             ->set('posts', ['wp_posts'])
             ->andReturn($date);
    }

    protected static function stubWpdbImpatNag()
    {
        global $wpdb;

        $time = Plugin::SECONDS_IN_DAY * (Plugin::SETTINGS_IMPAT_DAYS_DEFAULT + 1);
        $date = date('Y-m-d H:i:s', time() - $time);
        $wpdb = Mockery::mock();
        $wpdb->shouldReceive('prepare');
        $wpdb->allows('posts');
        $wpdb->shouldReceive('get_var')
             ->set('posts', ['wp_posts'])
             ->andReturn($date);
    }
}
