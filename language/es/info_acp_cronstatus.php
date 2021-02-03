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
	'ACP_CRON_STATUS_TITLE'	=> 'Estado del Cron',
	'LOG_CORE_INSTALLED'	=> 'Archivos cambiados correctamente',
	'LOG_CORE_DEINSTALLED'	=> 'Archivos cambiados de nuevo correctamente',
	'LOG_CORE_NOT_REPLACED'	=> 'No se pudo reemplazar el archivo, o archivo(s)<br />» %s',
	'LOG_CORE_NOT_UPDATED'	=> 'No se pudo actualizar el archivo, o archivo(s)<br />» %s',
	'FH_HELPER_NOTICE'		=> '¡La aplicación Forumhulp helper no existe!<br />Descargar <a href="https://github.com/ForumHulp/helper" target="_blank">forumhulp/helper</a> y copie la carpeta helper dentro de la carpeta de extensión forumhulp.',
	'CRONSTAT_NOTICE'		=> '<div class="phpinfo"><p class="entry">Esta extensión reside en %1$s » %2$s » %3$s.</p></div>',
));

// Description of Cron Status extension
$lang = array_merge($lang, array(
	'DESCRIPTION_PAGE'		=> 'Descripción',
	'DESCRIPTION_NOTICE'	=> 'Nota de la extensión',
	'ext_details' => array(
		'details' => array(
			'DESCRIPTION_1'		=> 'Visión general de trabajos Cron (con clasificación)',
			'DESCRIPTION_2'		=> 'Muestra el estado de cada tarea Cron',
			'DESCRIPTION_3'		=> 'Puede ejecutar cualquier tarea preparada manualmente',
			'DESCRIPTION_4'		=> 'Restablecer cronlock con un simple clic',
		),
		'note' => array(
			'NOTICE_1'			=> 'Noticia de Estado Cron (opcional)',
			'NOTICE_2'			=> 'Puede apagar esta opción en los Ajustes del Foro',
			'NOTICE_3'			=> 'Cron conmutables siempre',
			'NOTICE_4'			=> 'Preparado para phpBB 3.2'
		)
	)
));
