<?php

$wpdb = null;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Brickner\WPPostNag\Plugin;

// @TODO Refactor into cleaner format.
class WPStubs extends MockeryTestCase
{
    public static function mock($instance)
    {
        global $wpdb;

        switch ($instance) {
            case 'add_action':
                if ( ! function_exists('add_action')) {
                    function add_action()
                    {
                    }
                }
                break;
            case 'register_setting':
                if ( ! function_exists('register_setting')) {
                    function register_setting()
                    {
                    }
                }
                break;
            case 'add_settings_field':
                if ( ! function_exists('add_settings_field')) {
                    function add_settings_field()
                    {
                    }
                }
                break;
            case 'add_settings_section':
                if ( ! function_exists('add_settings_section')) {
                    function add_settings_section()
                    {
                    }
                }
                break;
            case 'get_option':
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
                break;
            case 'wpdb':
                $wpdb = Mockery::mock();
                $wpdb->shouldReceive('prepare');
                $wpdb->shouldReceive('get_var')->andReturn(0);
                break;
            case 'wpdb_default_nag':
                $wpdb = Mockery::mock();
                $wpdb->shouldReceive('prepare');
                $wpdb->shouldReceive('get_var')->andReturn(date('Y-m-d H:i:s'));
                break;
            case 'wpdb_pat_nag':
                $time = Plugin::SECONDS_IN_DAY * (Plugin::SETTINGS_PAT_DAYS_DEFAULT + 1);
                $date = date('Y-m-d H:i:s', time() - $time);
                $wpdb = Mockery::mock();
                $wpdb->shouldReceive('prepare');
                $wpdb->shouldReceive('get_var')->andReturn($date);
                break;
            case 'wpdb_impat_nag':
                $time = Plugin::SECONDS_IN_DAY * (Plugin::SETTINGS_IMPAT_DAYS_DEFAULT + 1);
                $date = date('Y-m-d H:i:s', time() - $time);
                $wpdb = Mockery::mock();
                $wpdb->shouldReceive('prepare');
                $wpdb->shouldReceive('get_var')->andReturn($date);
                break;
        }
    }
}