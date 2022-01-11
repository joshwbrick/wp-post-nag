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
    public function setUp(): void
    {
        require_once('tests/WPStubs.php');
    }

    public function testConstructorWPError()
    {
        try {
            new Plugin;
        } catch (Exception $e) {
            $this->assertEquals(Plugin::WP_MISSING_ERR, $e->getMessage());
        }
    }

    public function testConstructorAddAction()
    {
        WPStubs::stub('add_action');

        try {
            new Plugin;
        } catch (Exception $e) {
            $this->fail('Exception received:' . $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function testSetupSettingsWPErrors()
    {
        WPStubs::stub('add_action');

        try {
            $plugin = new Plugin;
            $plugin->setupSettings();
        } catch (Exception $e) {
            $this->assertEquals(Plugin::WP_MISSING_ERR, $e->getMessage());
        }
    }

    public function testSetupSettings()
    {
        WPStubs::stub('add_action');
        WPStubs::stub('register_setting');
        WPStubs::stub('add_settings_field');
        WPStubs::stub('add_settings_section');

        try {
            $plugin = new Plugin;
            $plugin->setupSettings();
        } catch (Exception $e) {
            $this->fail('Exception received:' . $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function testSettingsNumericalInputWPError()
    {
        WPStubs::stub('add_action');

        try {
            $plugin = new Plugin;
            $plugin->settingsNumericalInput('id', 'default');
        } catch (Exception $e) {
            $this->assertEquals(Plugin::WP_MISSING_ERR, $e->getMessage());
        }
    }

    public function testSettingsNumericalInput()
    {
        WPStubs::stub('add_action');
        WPStubs::stub('get_option');

        $output = '';
        try {
            $plugin = new Plugin;
            ob_start();
            $plugin->settingsNumericalInput('id', 'default');
            $output = ob_get_contents();
            ob_end_clean();
        } catch (Exception $e) {
            $this->fail('Exception received:' . $e->getMessage());
        }

        $validValue = '<input type="number" step="0.01" name="id" value="option_value">';

        $this->assertEquals($validValue, $output);
    }

    public function testGetNagNoPosts()
    {
        WPStubs::stub('add_action');
        WPStubs::stub('wpdb');
        WPStubs::stub('get_option');

        $output = '';
        try {
            $plugin = new Plugin;
            ob_start();
            $plugin->getNag();
            $output = ob_get_contents();
            ob_end_clean();
        } catch (Exception $e) {
            $this->fail('Exception received:' . $e->getMessage());
        }

        $this->assertStringContainsString(Plugin::INSTALLED_MSG, $output);
    }

    public function testGetNagPostsDefaultNag()
    {
        WPStubs::stub('add_action');
        WPStubs::stub('wpdb_default_nag');
        WPStubs::stub('get_option');

        $output = '';
        try {
            $plugin = new Plugin;
            ob_start();
            $plugin->getNag();
            $output = ob_get_contents();
            ob_end_clean();
        } catch (Exception $e) {
            $this->fail('Exception received:' . $e->getMessage());
        }

        $this->assertStringContainsString(Plugin::DEFAULT_NAG_MSG, $output);
    }

    public function testGetNagPostsPatientNag()
    {
        WPStubs::stub('add_action');
        WPStubs::stub('wpdb_pat_nag');
        WPStubs::stub('get_option');

        $output = '';
        try {
            $plugin = new Plugin;
            ob_start();
            $plugin->getNag();
            $output = ob_get_contents();
            ob_end_clean();
        } catch (Exception $e) {
            $this->fail('Exception received:' . $e->getMessage());
        }

        $this->assertStringContainsString('<i> 8 days since the last post.</i> 🙂', $output);
    }

    public function testGetNagPostsImpatientNag()
    {
        WPStubs::stub('add_action');
        WPStubs::stub('wpdb_impat_nag');
        WPStubs::stub('get_option');

        $output = '';
        try {
            $plugin = new Plugin;
            ob_start();
            $plugin->getNag();
            $output = ob_get_contents();
            ob_end_clean();
        } catch (Exception $e) {
            $this->fail('Exception received:' . $e->getMessage());
        }

        $this->assertStringContainsString('<b><i> 15 days since the last post</i></b> 🥱</p>', $output);
    }
}