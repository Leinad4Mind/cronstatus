<?php
/**
 *
 * @package       Cron Status
 * @copyright (c) 2014 - 2021 Igor Lavrov, John Peskens, Leinad4Mind
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ « » “ ” …
//

$lang = array_merge($lang, array(
	'CRON'								=> 'Cron',
	'CRON_LOCKED'						=> 'Cron Bloqueado',
	'CRON_TIME_LOCKED'					=> 'Fecha del bloqueo del Cron',
	'ACP_CRON_STATUS_TITLE'				=> 'Estado del Cron',
	'ACP_CRON_STATUS_EXPLAIN'			=> 'Estado del Cron es una página de su foro phpBB donde se puede comprobar si las tareas Cron están preparados para hacer. El “Auto” de la tarea con la última fecha, significa que la tarea tiene una opción de control de tiempo específico que no pudo ser reconocido por la extensión Estado del Cron. Una tarea marcada en rojo, significa una tarea que nunca se ha iniciado o que tiene un problema. Un bloqueo rojo, significa que esta tarea está bloqueada por el gerente Cron y bloquea otras tareas.',
	'CRON_STATUS_REFRESH'				=> 'Segundos para refrescar',
	'CRON_TASK_LOCKED'					=> 'Tarea Cron bloqueada',
	'CRON_STATUS_READY_TASKS'			=> 'Tareas preparadas para ejecutar',
	'CRON_STATUS_NOT_READY_TASKS'		=> 'No hay tareas preparadas',
	'CRON_STATUS_NEVER_STARTED_TASKS'	=> 'Never started tasks',
	'CRON_STATUS_NO_TASKS'				=> 'No hay tareas Cron disponibles',
	'CRON_STATUS_DATE_FORMAT'			=> 'Formato de fecha para la página del Estado del Cron',
	'CRON_STATUS_DATE_FORMAT_EXPLAIN'	=> 'El formato de fecha es el mismo que la función <code>date</code> de PHP.',
	'CRON_STATUS_MAIN_NOTICE'			=> 'Notice on the ACP index page',
	'CRON_STATUS_MAIN_NOTICE_EXPLAIN'	=> 'Mostrar Noticia del Estado del Cron en la página índice del PCA si el Cron esta bloqueado.',
	'CRON_STATUS_RUN_ALWAYS'			=> 'Ejecutar cron siempre',
	'CRON_STATUS_RUN_ALWAYS_EXPLAIN'	=> 'Ejecutar trabajos cron también disparado por robots. Funciona muy bien para foros no tan ocupados.',
	'CRON_TASK_NAME'					=> 'Nombre de la tarea',
	'CRON_TASK_DATE'					=> 'Última fecha de la tarea',
	'CRON_NEW_DATE'						=> 'Nueva fecha de la tarea',
	'CRON_TASK_NEVER_STARTED'			=> 'Nunca realizado',
	'CRON_TASK_AUTO'					=> 'Auto',
	'CRON_TASK_DATE_TIME'				=> 'Fecha y hora actual',
	'CRON_STATUS_ERROR'					=> 'Error de refresco',
	'CRON_STATUS_TIMEOUT'				=> 'Tiempo de espera de refresco',
	'CRON_STATUS_ERROR_EXPLAIN'			=> 'Se ha producido un error durante la actualización de la página.',
	'CRON_STATUS_DEVELOPERS'			=> 'Desarrolladores',
	'CRON_TASK_RUN'						=> 'Ejecutar',
	'CRON_TASK_RUNNING'					=> 'Ejecutando...',
	'CRON_TASK_RUN_NOW'					=> 'Ejecutar ahora',
	'CRON_TASK_RUN_ALL'					=> 'Ejecutar todo'
));
