<?php
/**
 *
 * @package       Cron Status
 * @copyright (c) 2014 - 2021 Igor Lavrov, John Peskens, Leinad4Mind
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace forumhulp\cronstatus\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	protected $config;
	protected $helper;
	protected $user;
	protected $request;
	protected $template;
	protected $db;
	protected $cron_manager;
	protected $phpbb_dispatcher;

	/**
	* Constructor
	*
	* @param \phpbb\config\config              $config           Config object
	* @param \phpbb\controller\helper          $helper           Controller helper object
	* @param \phpbb\user                       $user             User object
	* @param \phpbb\template\template          $template         Template object
	* @param \phpbb\db\driver\driver_interface $db               Database driver object
	* @param \phpbb\cron\manager               $cron_manager     Cron manager object
	* @param \phpbb\event\dispatcher           $phpbb_dispatcher Event dispatcher object
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\user $user, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\db\driver\driver_interface $db, \phpbb\cron\manager $cron_manager, \phpbb\event\dispatcher $phpbb_dispatcher)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->user = $user;
		$this->request = $request;
		$this->template = $template;
		$this->db = $db;
		$this->cron_manager = $cron_manager;
		$this->phpbb_dispatcher = $phpbb_dispatcher;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'					=> 'cron_always',
			'core.acp_main_notice'				=> 'load_cronstatus',
			'core.acp_board_config_edit_add'	=> 'add_config',
		);
	}

	public function cron_always()
	{
		$this->template->assign_vars(array(
			'RUN_CRON_ALWAYS' => $this->config['cronstatus_run_always']
		));
	}


	public function load_cronstatus($event)
	{
		$this->user->add_lang_ext('forumhulp/cronstatus', 'cronstatus');
		$tasks = $this->cron_manager->get_tasks();

		if (empty($tasks) || !$this->config['cron_lock'] || !$this->config['cronstatus_main_notice'])
		{
			return;
		}

		$time = explode(' ', $this->config['cron_lock']);

		$cronlock = '';
		$this->get_cron_tasks($cronlock, true);

		if ($cronlock)
		{
			$this->template->assign_vars(array(
				'CRON_TIME' => (sizeof($time) == 2) ? $this->user->format_date((int) $time[0], $this->config['cronstatus_dateformat']) : false,
				'CRON_NAME' => $cronlock
			));
		}
	}

	// array_search with partial matches
	public function array_find($needle, $haystack)
	{
		if (!is_array($haystack))
		{
			return false;
		}
		foreach ($haystack as $key => $item)
		{
			if (strpos($item['config_name'], $needle) !== false)
			{
				return $haystack[$key]['config_value'];
			}
		}
		return false;
	}

	public function add_config($event)
	{
		if ($event['mode'] == 'settings')
		{
			$this->user->add_lang_ext('forumhulp/cronstatus', 'cronstatus');
			$display_vars = $event['display_vars'];
			/* We add a new legend, but we need to search for the last legend instead of hard-coding */
			$submit_key = array_search('ACP_SUBMIT_CHANGES', $display_vars['vars']);
			$submit_legend_number = substr($submit_key, 6);
			$display_vars['vars']['legend'.$submit_legend_number] = 'ACP_CRON_STATUS_TITLE';
			$new_vars = array(
				'cronstatus_run_always'	=> array('lang' => 'CRON_STATUS_RUN_ALWAYS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'cronstatus_dateformat'	=> array('lang' => 'CRON_STATUS_DATE_FORMAT', 'validate' => 'string', 'type' => 'custom', 'method' => 'dateformat_select', 'explain' => true),
				'cronstatus_main_notice'	=> array('lang' => 'CRON_STATUS_MAIN_NOTICE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'legend'.($submit_legend_number + 1) => 'ACP_SUBMIT_CHANGES',
			);
			$display_vars['vars'] = phpbb_insert_config_array($display_vars['vars'], $new_vars, array('after' => $submit_key));
			$event['display_vars'] = $display_vars;
		}
	}

	public function get_cron_tasks(&$cronlock, $get_last_task = false)
	{
		$sql = "SELECT config_name, config_value FROM " . CONFIG_TABLE . " WHERE config_name LIKE " . (($get_last_task) ? "'%_last_gc' OR config_name = 'last_queue_run' ORDER BY config_value DESC" : "'%_gc' OR config_name = 'last_queue_run' OR config_name = 'queue_interval' OR config_name = 'autogroups_last_run' OR config_name = 'update_hashes_last_cron' OR config_name LIKE 'text_reparser%'");
		$result = ($get_last_task) ? $this->db->sql_query_limit($sql, 1) : $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$rows[] = array(
			"config_name"	=> "autogroups_check_last_gc", // This is the time of the last Cron Job.
			"config_value"	=> $this->array_find('autogroups_last_run', $rows)
		);
		$rows[] = array(
			"config_name"	=> "autogroups_check_gc", // This is the time of the last Cron Job.
			"config_value"	=> 86400
		);

		$sql = 'SELECT prune_next, prune_freq * 86400 AS prune_time FROM ' . FORUMS_TABLE . ' WHERE enable_prune = 1 ORDER BY prune_next';
		$result = $this->db->sql_query_limit($sql, 1);
		$prune = $this->db->sql_fetchrow($result);
		if ($prune)
		{
			$rows[] = array(
				"config_name"	=> "prune_forum_last_gc", // This is the time of the last Cron Job, not the time of pruned forums.
				"config_value"	=> $prune['prune_next'] - $prune['prune_time']
			);
			$rows[] = array(
				"config_name"	=> "prune_forum_gc",
				"config_value"	=> $prune['prune_time']
			);
		}
		$this->db->sql_freeresult($result);

		$sql = 'SELECT prune_shadow_next, prune_shadow_freq * 86400 AS prune_shadow_time FROM ' . FORUMS_TABLE . ' WHERE enable_shadow_prune = 1 ORDER BY prune_shadow_next';
		$result = $this->db->sql_query_limit($sql, 1);
		$prune_shadow = $this->db->sql_fetchrow($result);
		if ($prune_shadow)
		{
			$rows[] = array(
				"config_name"	=> "prune_shadow_topics_last_gc", // This is the time of the last Cron Job, not the time of pruned shadow topics.
				"config_value"	=> $prune_shadow['prune_shadow_next'] - $prune_shadow['prune_shadow_time']
			);
			$rows[] = array(
				"config_name"	=> "prune_shadow_topics_gc",
				"config_value"	=> $prune_shadow['prune_shadow_time']
			);
		}
		$this->db->sql_freeresult($result);

		$rows[] = array(
			"config_name"	=> "plupload_gc",
			"config_value"	=> 86400
		);

		if ($this->config['cron_lock'])
		{
			$cronlock = explode(' ', $this->config['cron_lock']);
			$cronlock = $cronlock[1];
			$cronlock = str_replace(array('_last_gc', 'prune_notifications', 'last_queue_run'), array('', 'prune_notifications', 'queue_interval'), $cronlock);
		}

		/**
		* Event to modify cron configuration variables before displaying cron information
		*
		* @event forumhulp.cronstatus.modify_cron_config
		* @var	array	rows			Configuration array
		* @var	string	cronlock		Name of the task that released cron lock (in last task date format)
		* @var	string	last_task_date	Last task date of the task that released cron lock
		* @since 3.1.0-RC3
		* @changed 3.1.2-RC Added last_task_date variable
		*/
		$vars = array('rows', 'cronlock', 'last_task_date');
		extract($this->phpbb_dispatcher->trigger_event('forumhulp.cronstatus.modify_cron_config', compact($vars)));

		return (!$get_last_task) ? $rows : true;
	}

	public function maxValueInArray($array, $keyToSearch)
	{
		$currentMax = null;
		foreach ($array as $arr)
		{
			foreach ($arr as $key => $value)
			{
				if (($key == $keyToSearch) && ($value >= $currentMax) && ((strrpos($arr['config_name'], '_last_gc') === strlen($arr['config_name']) - 8) || $arr['config_name'] === 'last_queue_run'))
				{
					$currentMax = $value;
					$currentName = $arr['config_name'];
				}
			}
		}
		return array('config_name' => $currentName , 'config_value' => $currentMax);
	}
}
