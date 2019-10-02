<?php

/**
 * index, Main application for Domain search
 *
 * @copyright Copyright (c) 2007 DotRoll Kft. (http://www.dotroll.com)
 * @author Zoltán Istvanovszki <zoltan.istvanovszki@dotroll.com>
 * @since 2016.09.20.
 * @package dotroll-domain-availability
 * @license https://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3
 */
\ini_set('error_reporting', \E_ALL ^ \E_DEPRECATED);
\error_reporting(\E_ALL ^ \E_DEPRECATED);
\date_default_timezone_set('Europe/Budapest');
\header('Content-Type: text/html; charset=utf-8');
include_once 'Domain.php';
$result = '';
if (!empty($_POST['domain'])) {
	$domain = new \Domain($_POST['domain']);
	$result = $domain->search();
	\ob_start();
	include_once 'result.php';
	$content = \ob_get_contents();
	\ob_end_clean();
	if (isset($_POST['ajax'])) {
		echo $content;
		die();
	}
}
include_once 'template.php';
