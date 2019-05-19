<?php
/**
*
* @package phpBB Extension - Delete PM from history | Удаление ЛС из истории
* @copyright (c) 2019 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\delete_pm_from_histoty\controller;

use Symfony\Component\HttpFoundation\Response;

class index
{
	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language */
	protected $lang;

	//** @var string phpbb_root_path */
	protected $phpbb_root_path;

	/** @var string phpEx */
	protected $php_ext;

	public function __construct(
		\phpbb\request\request_interface $request,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\auth\auth $auth,
		\phpbb\user $user,
		\phpbb\language\language $lang,
		$phpbb_root_path,
		$php_ext
	)
	{
		$this->request = $request;
		$this->db = $db;
		$this->auth = $auth;
		$this->user = $user;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->lang = $lang;
	}

	public function main()
	{		if (!$this->auth->acl_get('u_pm_delete'))
		{
			send_status_line(403, 'Forbidden');
			trigger_error('NO_AUTH_DELETE_MESSAGE');
		}

		$this->lang->add_lang('ucp');
		$msg_ids = $this->request->variable('mark', array(''));
		$cur_folder_id = 'inbox';

		if (sizeof($msg_ids))
		{
			if (confirm_box(true))
			{				include_once($this->phpbb_root_path . 'includes/functions_privmsgs.' . $this->php_ext);
				foreach ($msg_ids as $msg_id)
				{					$sql = 'SELECT folder_id
						FROM ' . PRIVMSGS_TO_TABLE . '
							WHERE msg_id = ' . $msg_id . '
								AND user_id = ' . $this->user->data['user_id'];
					$result = $this->db->sql_query($sql);
					$folder_id = $this->db->sql_fetchfield('folder_id');
					$this->db->sql_freeresult($result);
					delete_pm($this->user->data['user_id'], array($msg_id), $folder_id);
				}

				$success_msg = (count($msg_ids) == 1) ? 'MESSAGE_DELETED' : 'MESSAGES_DELETED';
				$redirect = append_sid("{$this->phpbb_root_path}ucp.$this->php_ext", 'i=pm&amp;folder=' . $cur_folder_id);

				meta_refresh(3, $redirect);
				trigger_error($this->user->lang[$success_msg] . '<br /><br />' . sprintf($this->user->lang['RETURN_FOLDER'], '<a href="' . $redirect . '">', '</a>'));

			}
			else
			{
				$s_hidden_fields = array(
					'mark_option'	=> 'delete_marked',
					'submit_mark'	=> true,
					'mark'			=> $msg_ids
				);

				confirm_box(false, 'DELETE_MARKED_PM', build_hidden_fields($s_hidden_fields));
			}
		}
		else
		{			trigger_error('NO_MESSAGES');
		}
	}
}