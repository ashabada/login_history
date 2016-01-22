Roundcube webmail plugin login_history
======================================
This plugin saves users login, date, time, ip and DNS name to MySQL every time user 
logs in. User and/or administrator can review historical login data. At the moment
only MySQL is supported.

Install
-------
* Go to plugins directory.
* Run "git clone https://github.com/ashabada/login_history/".
* Create additional MySQL table from login_history/SQL/mysql.initial.sql

Config
------
* Change how many records are shown in config.inc.php. By default last 100 are shown.

License
-------
This plugin is released under the [GNU General Public License Version 3+](http://www.gnu.org/licenses/gpl-3.0.txt)
