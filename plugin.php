<?php
/**
 * Plugin Name: WP Post Nag
 * Description: Have you been blogging at the frequency you want too? Let WP Post Nag keep you honest.
 * Version: 1.0.0
 * Requires at least: 5.2
 * Requires PHP: 7.4
 * Author: Josh Brickner
 * License: MIT
 * Copyright: 2022 Josh Brickner <josh@brickner.dev>
 */

try {
    require_once("vendor/autoload.php");
    new Brickner\WPPostNag\Plugin;
} catch (Exception $e) {
    error_log($e->getMessage());
}
