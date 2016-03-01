<?php
/**
 * Login_history
 *
 * Saves login name, IP address and DNS name to database.
 *
 * @version 1.04
 * @license GNU GPLv3+
 * @author ashabada
 */
class login_history extends rcube_plugin

	{
	public

	function init()
		{
		$this->add_hook('login_after', array(
			$this,
			'login_after'
		));
		$this->add_hook('template_object_username', array(
			$this,
			'login_info'
		));
		$this->add_texts('localization/', true);
		$this->register_action('plugin.login_history', array(
			$this,
			'loginbody'
		));
		}

	function login_after()
		{
		$rcmail = rcmail::get_instance();
		$user = $rcmail->user;
		$res = $rcmail->get_dbh()->query("SELECT remoteip, logintime FROM login_logs 
						WHERE username = ? ORDER BY logintime DESC LIMIT 1", $user->data['username']);
		$_SESSION["remoteip"] = '-';
		$_SESSION["logintime"] = '-';
		if (($data = $rcmail->get_dbh()->fetch_assoc($res)))
			{
			$_SESSION["remoteip"] = $data['remoteip'];
			$_SESSION["logintime"] = $data['logintime'];
			}

		$username = $user->data['username'];
		$remoteip = rcube_utils::remote_ip();
                if(strpos($remoteip, '('))
                        {
                        $remoteip = substr($remoteip, 0, strpos($remoteip, '('));
                        }
                $remotedns = gethostbyaddr($remoteip);
		if ($remoteip == $remotedns)
			{
			$remotedns = "----";
			}

		$rcmail->get_dbh()->query("INSERT INTO login_logs(username, remoteip, remotedns, logintime) 
						VALUES(?, ?, ?, NOW())", $username, $remoteip, $remotedns);
		}

	public

	function login_info($p)
		{
		$rcmail = rcmail::get_instance();
		$user = $rcmail->user;
		$username = $user->data['username'];
		return array(
			'content' => Q($username) . " (" . Q($this->gettext('lastlogin')) . 
			'<a href="./?_action=plugin.login_history" class="about-link">' . 
			Q($_SESSION['remoteip']) . " " . Q($_SESSION['logintime']) . "</a>" . ")"
		);
		}

	function loginbody()
		{
		$this->register_handler('plugin.body', array(
			$this,
			'showinfo'
		));
		rcmail::get_instance()->output->send('plugin');
		}

	function showinfo()
		{
		$this->load_config();
		$rcmail = rcmail::get_instance();
		$limit = $rcmail->config->get('login_history_count');
                $enable_geoip = $rcmail->config->get('login_history_geoip');
                $user = $rcmail->user;

                if ($enable_geoip == '1')
                        {
                        $this->include_stylesheet('flags.css');
                        $table = new html_table(array(
                                'cols' => 4,
                                'cellpadding' => 4
                        ));
                        $table->add('title', rcube::Q($this->gettext('logintime')) , $mode = 'strict');
                        $table->add('title', rcube::Q($this->gettext('ip')) , $mode = 'strict');
                        $table->add('title', rcube::Q($this->gettext('dns')) , $mode = 'strict');
                        $table->add('title', rcube::Q($this->gettext('country')) , $mode = 'strict');

                        $res = $rcmail->get_dbh()->query("SELECT remoteip, remotedns, logintime FROM login_logs
                                                        WHERE username = ? ORDER BY logintime DESC LIMIT $limit", $user->data['username']);
                        while (($data = $rcmail->get_dbh()->fetch_assoc($res)))
                                {
                                $table->add('', rcube::Q($data['logintime'], $mode = 'strict'));
                                $table->add('', rcube::Q($data['remoteip'], $mode = 'strict'));
                                $table->add('', rcube::Q($data['remotedns'], $mode = 'strict'));
                                $country_code=strtolower(geoip_country_code_by_name($data['remoteip']));
                                $table->add('', "<img src=\"plugins/login_history/img/blank.gif\" class=\"flag flag-". $country_code . "\" alt=\"" . $country_code . "\" />" , $mode =$
                                }

                        return html::tag('h4', null, rcube::Q($this->gettext('loginhistory') . $user->get_username() , $mode = 'strict')) . $table->show();
                        }
                else
                        {


                        $table = new html_table(array(
                                'cols' => 3,
                                'cellpadding' => 3
                        ));
                        $table->add('title', rcube::Q($this->gettext('logintime')) , $mode = 'strict');
                        $table->add('title', rcube::Q($this->gettext('ip')) , $mode = 'strict');
                        $table->add('title', rcube::Q($this->gettext('dns')) , $mode = 'strict');
                        $res = $rcmail->get_dbh()->query("SELECT remoteip, remotedns, logintime FROM login_logs
                                                        WHERE username = ? ORDER BY logintime DESC LIMIT $limit", $user->data['username']);
                        while (($data = $rcmail->get_dbh()->fetch_assoc($res)))
                                {
                                $table->add('', rcube::Q($data['logintime'], $mode = 'strict'));
                                $table->add('', rcube::Q($data['remoteip'], $mode = 'strict'));
                                $table->add('', rcube::Q($data['remotedns'], $mode = 'strict'));
                                }
                        return html::tag('h4', null, rcube::Q($this->gettext('loginhistory') . $user->get_username() , $mode = 'strict')) . $table->show();
                        }
                }
        }

?>

