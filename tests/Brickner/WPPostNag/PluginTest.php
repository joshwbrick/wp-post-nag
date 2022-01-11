<?php
declare(strict_types=1);

namespace Brickner\WPPostNag;

use Exception;
use PHPUnit\Framework\TestCase;
use WPStubs;

// @TODO Run coverage
// @TODO Setup CI on GitHub

class PluginTest extends TestCase
{
    public function testConstructorWPError()
    {
        try {
            $plugin = new Plugin;
        } catch (Exception $e) {
            $this->assertEquals(Plugin::WP_MISSING_ERR, $e->getMessage());
        }
    }

    public function testConstructorAddAction()
    {
        WPStubs::mock('add_action');

        try {
            $plugin = new Plugin;
        } catch (Exception $e) {
            $this->fail('Exception received:' . $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function testSetupSettingsWPErrors()
    {
        WPStubs::mock('add_action');

        try {
            $plugin = new Plugin;
            $plugin->setupSettings();
        } catch (Exception $e) {;
            $this->assertEquals(Plugin::WP_MISSING_ERR, $e->getMessage());
        }
    }

    public function testSetupSettings()
    {
        WPStubs::mock('add_action');
        WPStubs::mock('register_setting');
        WPStubs::mock('add_settings_field');
        WPStubs::mock('add_settings_section');

        try {
            $plugin = new Plugin;
            $plugin->setupSettings();
        } catch (Exception $e) {;
            $this->fail('Exception received:' . $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function testSettingsNumericalInputWPError()
    {
        WPStubs::mock('add_action');

        try {
            $plugin = new Plugin;
            $plugin->settingsNumericalInput('id', 'default');
        } catch (Exception $e) {;
            $this->assertEquals(Plugin::WP_MISSING_ERR, $e->getMessage());
        }
    }

    public function testSettingsNumericalInput()
    {
        WPStubs::mock('add_action');
        WPStubs::mock('get_option');

        $output = '';
        try {
            $plugin = new Plugin;
            ob_start();
            $plugin->settingsNumericalInput('id', 'default');
            $output = ob_get_contents();
            ob_end_clean();
        } catch (Exception $e) {;
            $this->fail('Exception received:' . $e->getMessage());
        }

        $validValue = '<input type="number" step="0.01" name="id" value="option_value">';

        $this->assertEquals($validValue, $output);
    }

    public function testGetNagNoPosts()
    {
        WPStubs::mock('add_action');
        WPStubs::mock('wpdb');
        WPStubs::mock('get_option');

        $output = '';
        try {
            $plugin = new Plugin;
            ob_start();
            $plugin->getNag();
            $output = ob_get_contents();
            ob_end_clean();
        } catch (Exception $e) {;
            $this->fail('Exception received:' . $e->getMessage());
        }

        $this->assertStringContainsString(Plugin::INSTALLED_MSG, $output);
    }

    public function testGetNagPostsDefaultNag()
    {
        WPStubs::mock('add_action');
        WPStubs::mock('wpdb_default_nag');
        WPStubs::mock('get_option');

        $output = '';
        try {
            $plugin = new Plugin;
            ob_start();
            $plugin->getNag();
            $output = ob_get_contents();
            ob_end_clean();
        } catch (Exception $e) {;
            $this->fail('Exception received:' . $e->getMessage());
        }

        $this->assertStringContainsString(Plugin::DEFAULT_NAG_MSG, $output);
    }

    public function testGetNagPostsPatientNag()
    {
        WPStubs::mock('add_action');
        WPStubs::mock('wpdb_pat_nag');
        WPStubs::mock('get_option');

        $output = '';
        try {
            $plugin = new Plugin;
            ob_start();
            $plugin->getNag();
            $output = ob_get_contents();
            ob_end_clean();
        } catch (Exception $e) {;
            $this->fail('Exception received:' . $e->getMessage());
        }

        $this->assertStringContainsString('<i> 8 days since the last post.</i> 🙂', $output);
    }

    public function testGetNagPostsImpatientNag()
    {
        WPStubs::mock('add_action');
        WPStubs::mock('wpdb_impat_nag');
        WPStubs::mock('get_option');

        $output = '';
        try {
            $plugin = new Plugin;
            ob_start();
            $plugin->getNag();
            $output = ob_get_contents();
            ob_end_clean();
        } catch (Exception $e) {;
            $this->fail('Exception received:' . $e->getMessage());
        }

        $this->assertStringContainsString('<b><i> 15 days since the last post</i></b> 🥱</p>', $output);
    }
}