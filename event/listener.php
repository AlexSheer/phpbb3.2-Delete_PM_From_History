<?php
/**
*
* @package phpBB Extension - Delete PM from history | Удаление ЛС из истории
* @copyright (c) 2019 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace sheer\delete_pm_from_histoty\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
/**
* Assign functions defined in this class to event listeners in the core
*
* @return array
* @static
* @access public
*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.ucp_pm_view_messsage'	=> 'ucppm_view_messsage',
		);
	}

	/** @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/**
	* Constructor
	*/
	public function __construct(
		\phpbb\template\template $template,
		\phpbb\controller\helper $helper
	)
	{
		$this->template = $template;
		$this->helper   = $helper;
	}

	public function ucppm_view_messsage($event)
	{		$this->template->assign_vars(array(
			'DELETE_ACTION' 	=> $this->helper->route('sheer_delete_pm_from_histoty_controller', array('folder_id' => $event['folder_id'])),
		));
	}
}
