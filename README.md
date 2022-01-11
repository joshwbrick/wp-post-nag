# WP Post Nag

[![Build & Tests](https://github.com/joshwbrick/wp-post-nag/actions/workflows/github-php-actions.yml/badge.svg?branch=master&event=push)](https://github.com/joshwbrick/wp-post-nag/actions/workflows/github-php-actions.yml)

Have you been blogging at the frequency you want too? Let WP Post Nag keep you honest.

## Screenshots

Below are some screenshots demonstrating how the plugin works.

Users can set a number of days that the plugin should be patient with them as well as a number of days after which the
gloves come off 😊. Different "nag" messages appear depending on the time since the last post and the user's settings.

### Example Nag
![!Nag Screen](/imgs/nag-screen.png)

### Nag Settings

![Nag Screen](/imgs/settings-screen-normal.png)

![!Nag Screen](/imgs/settings-screen-patient.png)

![!Nag Screen](/imgs/settings-screen-impatient.png)

## Development

### Docker

This repository comes with its own docker based development server. Simply run the following command to boot it:

    $ cd wp-post-nag
    $ docker-compose up -d

### Dependencies

There are zero runtime dependencies for this project. However, PHPUnit is the sole development dependency.


## License

This plugin has been released under the MIT license. See included license file for more information.
