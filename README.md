# Wordpress Integration #

This plugin enables automatic integration between Moodle and WordPress (with WooCommerce), facilitating the creation and enrollment of users in Moodle courses directly from purchases made in WordPress.
When a user purchases a course in WooCommerce, the WordPress site sends a request to Moodle via a secure web service (REST API).The plugin in Moodle receives this request, automatically creates the user (if they do not already exist), and enrolls them in the corresponding course.In this way, sales management and course access are carried out without manual intervention, improving the user experience and the efficiency of the educational process.


## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/local/wordpress_integration

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

2025 Andrés Felipe Robin <afrgmt@gmail.com>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
