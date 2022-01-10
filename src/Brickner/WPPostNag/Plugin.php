<?php /** @noinspection PhpUndefinedFunctionInspection */

namespace Brickner\WPPostNag;

use Exception;

# Import WordPress functions from global namespace.

use function add_action;
use function register_setting;
use function add_settings_field;
use function add_settings_section;
use function get_option;

class Plugin
{

    # Numerical Constants
    const SECONDS_IN_DAY = 86_400;
    const SECONDS_IN_HOUR = 3_600;
    const SECONDS_IN_MINUTE = 60;

    # User Message & Label Constants
    const WP_MISSING_ERR = 'Expected WP plugin facilities are missing. Please check the installation of WP Post Nag.';
    const INSTALLED_MSG = ' ✅ WP Post Nag is installed. (nothing posted yet 🦗)';
    const DEFAULT_NAG_MSG = 'There are recent posts. 😇';
    const PAT_NAG_MSG = '<i>%s since the last post.</i> 🙂';
    const IMPAT_NAG_MSG = '<b><i>%s since the last post</i></b> 🥱';

    const DAY_LABEL = ' %d days';
    const HOUR_LABEL = ' %d hours';
    const MINUTE_LABEL = ' %d minutes';

    # Settings Constants
    const SETTINGS_PAGE = 'writing';

    const SETTINGS_SECTION = 'wp_post_nag';
    const SETTINGS_SECTION_HEADER = 'WP Post Nag';
    const SETTINGS_SECTION_DESC = '<p>Decimal values are accepted to allow representation of partial days.</p>';

    const SETTINGS_PAT_ID = 'wp_post_nag_patient';
    const SETTINGS_PAT_LABEL = 'Be Patient for (days)';
    const SETTINGS_PAT_DAYS_DEFAULT = 7.0;

    const SETTINGS_IMPAT_ID = 'wp_post_nag_impatient';
    const SETTINGS_IMPAT_LABEL = 'Be Impatient after (days)';
    const SETTINGS_IMPAT_DAYS_DEFAULT = 14.0;

    # HTML Template Constants
    const NAG_HTML_TEMPLATE = '<p class="alignright">&nbsp;&ndash;%s</p>';
    const SETTINGS_NUMERICAL_INPUT_HTML = '<input type="number" step="0.01" name="%s" value="%s">';

    # SQL Query Constants
    const LAST_POSTED_DATE_SQL = "SELECT MAX(post_date) FROM %s WHERE post_status = %%s AND post_type = %%s";
    const FILTER_STATUS = 'publish';
    const FILTER_TYPE = 'post';

    /**
     * @throws Exception
     */
    public function __construct()
    {

        if (function_exists('add_action')) {
            add_action('admin_init', [$this, 'setupSettings']);
            add_action('in_admin_footer', [$this, 'getNag']);
        } else {
            throw new Exception(self::WP_MISSING_ERR);
        }
    }

    /**
     * @throws Exception
     */
    public function setupSettings()
    {

        if (
            function_exists('register_setting')
            && function_exists('add_settings_field')
            && function_exists('add_settings_section')
        ) {

            register_setting(self::SETTINGS_PAGE, self::SETTINGS_PAT_ID, 'floatval');
            register_setting(self::SETTINGS_PAGE, self::SETTINGS_IMPAT_ID, 'floatval');

            add_settings_section(
                self::SETTINGS_SECTION,
                self::SETTINGS_SECTION_HEADER,
                fn() => print(self::SETTINGS_SECTION_DESC),
                self::SETTINGS_PAGE
            );

            add_settings_field(
                self::SETTINGS_PAT_ID,
                self::SETTINGS_PAT_LABEL,
                fn() => $this->settingsNumericalInput(self::SETTINGS_PAT_ID, self::SETTINGS_PAT_DAYS_DEFAULT),
                self::SETTINGS_PAGE,
                self::SETTINGS_SECTION
            );

            add_settings_field(
                self::SETTINGS_IMPAT_ID,
                self::SETTINGS_IMPAT_LABEL,
                fn() => $this->settingsNumericalInput(self::SETTINGS_IMPAT_ID, self::SETTINGS_IMPAT_DAYS_DEFAULT),
                self::SETTINGS_PAGE,
                self::SETTINGS_SECTION
            );

        } else {
            throw new Exception(self::WP_MISSING_ERR);
        }
    }

    /**
     * @throws Exception
     */
    public function settingsNumericalInput($id, $default)
    {
        $value = $this->getOption($id, $default);
        echo sprintf(self::SETTINGS_NUMERICAL_INPUT_HTML, $id, $value);
    }

    /**
     * @throws Exception
     */
    public function getNag()
    {

        $lastPostDate   = $this->queryLastPostDate(self::FILTER_STATUS, self::FILTER_TYPE);
        $timeOfLastPost = strtotime($lastPostDate); # Returns bool `false` if time not determined.

        $out = self::INSTALLED_MSG;
        if (false !== $timeOfLastPost) { # Ensure a valid time was determined.
            $timeSinceLastPost = time() - $timeOfLastPost;

            $patDays   = $this->getPatientDaysOption() * self::SECONDS_IN_DAY;
            $impatDays = $this->getImpatientDaysOption() * self::SECONDS_IN_DAY;

            if ($timeSinceLastPost > $impatDays) {
                $message = self::IMPAT_NAG_MSG;
            } elseif ($timeSinceLastPost > $patDays) {
                $message = self::PAT_NAG_MSG;
            } else {
                $message = self::DEFAULT_NAG_MSG;
            }

            $timeIntervals   = $this->timeToDaysHoursMinutes($timeSinceLastPost);
            $timeLabels      = join(array_keys($timeIntervals));
            $labeledInterval = sprintf($message, $timeLabels);
            $out             = vsprintf($labeledInterval, $timeIntervals);
        }

        echo sprintf(self::NAG_HTML_TEMPLATE, $out);
    }

    protected function queryLastPostDate(string $status, string $type): string
    {

        global $wpdb;

        # Get the name of the `posts` table from WordPress, no user input here.
        $queryTemplate = sprintf(self::LAST_POSTED_DATE_SQL, $wpdb->posts);

        # Future use cases may send user input to this method. Sanitize method inputs just in case.
        $preparedQuery = $wpdb->prepare(
            $queryTemplate,
            $status,
            $type
        );

        $postedDate = $wpdb->get_var($preparedQuery);

        if ( ! is_string($postedDate) || empty($postedDate)) {
            return '';
        }

        return $postedDate;
    }

    protected function timeToDaysHoursMinutes($time): array
    {

        $days    = floor($time / self::SECONDS_IN_DAY);
        $hours   = floor(($time % self::SECONDS_IN_DAY) / self::SECONDS_IN_HOUR);
        $minutes = floor(($time % self::SECONDS_IN_HOUR) / self::SECONDS_IN_MINUTE);

        return array_filter([
            self::DAY_LABEL    => $days,
            self::HOUR_LABEL   => $hours,
            self::MINUTE_LABEL => $minutes
        ]);
    }

    /**
     * @throws Exception
     */
    protected function getPatientDaysOption()
    {
        return $this->getOption(self::SETTINGS_PAT_ID, self::SETTINGS_PAT_DAYS_DEFAULT);
    }

    /**
     * @throws Exception
     */
    protected function getImpatientDaysOption()
    {
        return $this->getOption(self::SETTINGS_IMPAT_ID, self::SETTINGS_IMPAT_DAYS_DEFAULT);
    }

    /**
     * @throws Exception
     */
    protected function getOption($id, $default)
    {
        if (function_exists('get_option')) {
            return get_option($id, $default);
        } else {
            throw new Exception(self::WP_MISSING_ERR);
        }
    }
}
