<?php
/**
 *
 * @package       Cron Status
 * @copyright (c) 2014 - 2021 Igor Lavrov, John Peskens, Leinad4Mind
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace forumhulp\cronstatus\acp;

class cronstatus_helper
{
	/**
	 * Recursive array sorting based on the second level key
	 *
	 * @param array  $array Array to be sorted
	 * @param string $on    Second level key for sorting
	 * @param int    $order Sorting direction (SORT_ASC, SORT_DESC)
	 * @return array
	 */
	public function array_sort($array, $on, $order = SORT_ASC)
	{
		$new_array = array();
		$sortable_array = array();

		if (sizeof($array) > 0)
		{
			foreach ($array as $k => $v)
			{
				if (is_array($v))
				{
					foreach ($v as $k2 => $v2)
					{
						if ($k2 == $on)
						{
							$sortable_array[$k] = $v2;
						}
					}
				}
				else
				{
					$sortable_array[$k] = $v;
				}
			}

			switch ($order)
			{
				case SORT_ASC:
					asort($sortable_array);
				break;
				case SORT_DESC:
					arsort($sortable_array);
				break;
			}

			foreach ($sortable_array as $k => $v)
			{
				$new_array[$k] = $array[$k];
			}
		}
		return $new_array;
	}


	/**
	 * Performs the search for a specific config_name and
	 * returns the corresponding config_value or false if nothing was found
	 * Works like array_search with partial matches
	 *
	 * @param string $needle   The config_name to search for
	 * @param array  $haystack The array to search in
	 * @return mixed
	 */
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


	/**
	 * Identifies current Cron task's name and the time of its last execution
	 *
	 * @param array   $rows      Array with fetched parameters for Cron tasks
	 * @param string &$name		 Current Cron task's name (to be properly formatted)
	 * @param int    &$task_date Current Cron task's time of its last execution
	 */
	public function get_task_params(array $rows, &$name, &$task_date)
	{
		$find = strpos($name, 'tidy');
		if ($find !== false)
		{
			$name = substr($name, $find + 5);
			$name = ($name == 'sessions') ? 'session' : $name;
			$task_date = (int) $this->array_find($name . '_last_gc', $rows);
		}
		else if (strpos($name, 'prune_notifications'))
		{
			$task_date = (int) $this->array_find('read_notification_last_gc', $rows);
			$name = 'read_notification';
		}
		else if (strpos($name, 'queue'))
		{
			$task_date = (int) $this->array_find('last_queue_run', $rows);
			$name = 'queue_interval';
		}
		else if (strpos($name, 'text_reparser'))
		{
			$task_date = (int) $this->array_find(str_replace('cron.task.', '', $name) . '_last_cron', $rows);
			$name = $name;
		}
		else if (strpos($name, 'update_hashes'))
		{
			$task_date = (int) $this->array_find(str_replace('cron.task.core.', '', $name) . '_last_cron', $rows);
			$name = $name;
		}
		else
		{
			$name = (strrpos($name, ".") !== false) ? substr($name, strrpos($name, ".") + 1) : $name;
			$task_last_gc = $this->array_find($name . '_last_gc', $rows);
			$task_date = ($task_last_gc !== false) ? (int) $task_last_gc : 0;
		}
	}


	/**
	 * Calculates next time for Cron task execution
	 *
	 * @param array  $rows      Array with fetched parameters for Cron tasks
	 * @param int    $task_date Microtime of last execution of current Cron task
	 * @param string $name      Name of current Cron task
	 * @return int
	 */
	public function get_new_task_date(array $rows, $task_date, $name)
	{
		$new_task_interval = ($task_date > 0) ? $this->array_find($name . (($name != 'queue_interval') ? '_gc' : ''), $rows) : 0;
		$new_task_interval = ($task_date > 0) ? $this->array_find(str_replace('cron.task.', '', $name) . (($name == 'text_reparser') ? '_cron_interval': ''), $rows) : 0;
		$new_task_date = ($new_task_interval > 0) ? $task_date + $new_task_interval : 0;
		return $new_task_date;
	}
}
