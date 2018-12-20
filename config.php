<?php
defined('ROOT_PATH') or exit('ROOT_PATH is not found!');

/**
 * @package     Milou_v1
 * @author      Emre Can ÖZTAŞ (ecoz) <oztasemrecan@gmail.com>
 * @copyright   Copyright (c) 2018, Emre Can ÖZTAŞ. (https://emrecanoztas.com/)
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://github.com/oztasemrecan/php-ping
 * @since       Version 1.0.0
 */

// php.ini Settings.
error_reporting(E_ALL);
ini_set('default_charset', 'utf-8');
ini_set('memory_limit', -1);
ini_set('max_execution_time', -1);
ini_set('max_input_time', -1);
ini_set('file_uploads', 1);
ini_set('upload_max_filesize', '5M');
ini_set('session.gc_maxlifetime', 14400);
ini_set('date.timezone', 'Europe/Istanbul');
ini_set('expose_php', -1);
ini_set('allow_url_fopen', -1);
ini_set('allow_url_include', -1);
ini_set('disable_functions', 'exec, passthru, shell_exec, system, proc_open, popen, curl_exec, curl_multi_exec, parse_ini_file, show_source');
gc_enable();

// System settings (Database, HTTP Header, User Agent and Regex).
const SYSTEM_SETTINGS = array(
	'data_file' => 'data.txt',
	'database' => array(
		'charset' => 'utf8',
		'auth' => array(
			'hostname' => 'localhost',
			'username' => 'root',
			'password' => ''
		),
		'info' => array(
			'db_name'     => 'db_firma',
			'db_table'    => 'tbl_firma',
			'tbl_columns' => array('firma_adi', 'sehir', 'adres', 'telefon_1', 'telefon_2', 'website', 'url')
		)
	),
	'http_header' => array(
		'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
		'Cache-Control: max-age=0',
		'Connection: keep-alive',
		'Keep-Alive: 300',
		'Accept-Charset: ISO-8859-9,utf-8;q=0.7,*;q=0.7',
		'Accept-Language: en-us,en;q=0.5',
		'Pragma: '
	),
	'user_agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
	'reg_clear' => array(
        '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
        '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
        '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
	),
	'reg_url' => '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i'
);
