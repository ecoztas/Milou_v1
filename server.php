<?php
header("Content-Type: text/html; charset=utf-8");

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
ini_set('memory_limit', -1);
ini_set('max_execution_time', -1);
gc_enable();

// Define constant.
defined('ROOT_PATH') or define('ROOT_PATH', realpath(__DIR__));
defined('DIR_SEP') or define('DIR_SEP', '/');

// Include file(s).
include(ROOT_PATH . DIR_SEP . 'config.php');

// Define global variable(s).
$page_details = array();

/** Main block */
if (php_sapi_name() === 'cli') {
	!curl_version() ? exit('cURL is not found!') : true; // cURL control
	$file = ROOT_PATH . DIR_SEP . SYSTEM_SETTINGS['data_file'];
	if (file_exists($file)) {
		$page = file_get_contents($file); // read data.txt
		if (!empty($page)) {
			$page_details = array_map('trim', explode(',', $page));
            $base_url     = $page_details[0];
            if (preg_match(SYSTEM_SETTINGS['reg_url'], $base_url)) {
	            $const_url    = parse_url($base_url)['scheme'] . '://' . parse_url($base_url)['host'];
				array_shift($page_details);

				crawler($base_url, $const_url); // Start
            } else {
            	exit('$base_url is not URL!');
            }
		} else {
			exit(SYSTEM_SETTINGS['data_file'] . ' is empty!');
		}
	} else {
		exit(SYSTEM_SETTINGS['data_file'] . ' is not found!');
	}
} else {
	exit('This is for CLI programmers not for browserBoys!');
}

/**
 * Crawler method is crawling URL from web page.
 * @param  string $base_url
 * @param  string $const_url
 * @return void
 */
function crawler($base_url, $const_url)
{
	global $page_details;
	static $found_url = array();
	static $visited_url = array();

	$curl = curl_init($base_url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_USERAGENT, SYSTEM_SETTINGS['user_agent']);
	curl_setopt($curl, CURLOPT_HTTPHEADER, SYSTEM_SETTINGS['http_header']);
	curl_setopt($curl, CURLOPT_ENCODING, '');
	$html = curl_exec($curl);
	curl_close($curl);

	unset($curl);

	libxml_use_internal_errors(true);
	$document = new DOMDocument();
	@$document->loadHTML($html);

	$a_list = $document->getElementsByTagName('a');

	if (is_object($a_list)) {
		foreach ($a_list as $a) {
			$a_href = $a->getAttribute('href');

			if (substr($a_href, 0, 1) == "/" && substr($a_href, 0, 2) != "//") {
				$a_href = parse_url($base_url)["scheme"] . "://" . parse_url($base_url)["host"] . $a_href;
			} else if (substr($a_href, 0, 2) == "//") {
				$a_href = parse_url($base_url)["scheme"] . ":" . $a_href;
			} else if (substr($a_href, 0, 2) == "./") {
				$a_href = parse_url($base_url)["scheme"] . "://" . parse_url($base_url)["host"] . dirname(parse_url($base_url)["path"]).substr($a_href, 1);
			} else if (substr($a_href, 0, 1) == "#") {
				$a_href = parse_url($base_url)["scheme"] . "://" . parse_url($base_url)["host"] . parse_url($base_url)["path"] . $a_href;
			} else if (substr($a_href, 0, 3) == "../") {
				$a_href = parse_url($base_url)["scheme"]."://".parse_url($base_url)["host"]."/".$a_href;
			} else if (substr($a_href, 0, 11) == "javascript:") {
				continue;
			} else if (substr($a_href, 0, 5) != "https" && substr($a_href, 0, 4) != "http") {
				$a_href = parse_url($base_url)["scheme"] . "://" . parse_url($base_url)["host"] . "/" . $a_href;
			}

			if (!in_array($a_href, $visited_url)) {
				$parse_url = parse_url($a_href)['scheme'] . '://' . parse_url($a_href)['host'];
				if ($const_url === $parse_url) {
					$found_url[]   = $a_href;
					$visited_url[] = $a_href;

					!empty($page_details) ? scraper($a_href) : null;
				}
			}
		}
	}

	array_shift($found_url);

	if (count($found_url > 0)) { // Go on
		foreach ($found_url as $base_url) {
			crawler($base_url, $const_url);
		}	
	} else { // Finished
		echo(PHP_EOL);
		echo('Crawling finished');
		echo(PHP_EOL);
	}
}

/**
 * Scraper method is scraping data from web page.
 * @param  string $base_url
 * @return void
 */
function scraper($base_url)
{
	global $page_details;
	static $page_number = 0;

	$page_content = array();

	$curl = curl_init($base_url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_USERAGENT, SYSTEM_SETTINGS['user_agent']);
	curl_setopt($curl, CURLOPT_HTTPHEADER, SYSTEM_SETTINGS['http_header']);
	curl_setopt($curl, CURLOPT_ENCODING, '');
	$html = curl_exec($curl);
	curl_close($curl);

	unset($curl);

    $page_encoding = strtolower(mb_detect_encoding($html));
    $page_encoding != 'utf-8' ? $html = mb_convert_encoding($html, 'ISO-8859-1', 'utf-8') : null;

	libxml_use_internal_errors(true);
	$document = new DOMDocument();
	@$document->loadHTML($html);
	$xpath = new DOMXPath($document);

	echo(PHP_EOL . $base_url . PHP_EOL);

	$control_first_data = @$xpath->query(trim($page_details[0]))->item(0)->textContent; // Data control: first field

	if (!empty($control_first_data)) {
		foreach ($page_details as $detail) {
			$data = (string)trim(@$xpath->query(trim($detail))->item(0)->textContent);
			$data = sanitize($data);
			!empty(trim($data)) ? array_push($page_content, $data) : array_push($page_content, '-');
		}

		array_push($page_content, $base_url);
		print_r($page_content);
		database($page_content);
	}
}

/**
 * Sanitize datas.
 * @param  string $input
 * @return string
 */
function sanitize($input)
{
	return(preg_replace(SYSTEM_SETTINGS['reg_clear'], '', $input));
}

/**
 * Database connection and saving records
 * @param  array $records 
 * @return void
 */
function database($records)
{
	static $connection = null;

	// ---> Connection control
	if (!@mysqli_ping($connection)) {
		$connection = mysqli_connect(
			SYSTEM_SETTINGS['database']['auth']['hostname'],
			SYSTEM_SETTINGS['database']['auth']['username'],
			SYSTEM_SETTINGS['database']['auth']['password'],
			SYSTEM_SETTINGS['database']['info']['db_name']
		);

		if (mysqli_connect_errno()) {
			exit('Connection is failed! ' . mysqli_error($connection));
		} else {
			mysqli_set_charset($connection, SYSTEM_SETTINGS['database']['charset']);
			mysqli_query($connection, "SET NAMES "  . SYSTEM_SETTINGS['database']['charset']);
		}		
	}
	// Connection control <---

	$columns = implode(', ', SYSTEM_SETTINGS['database']['info']['tbl_columns']);
	$records = '\'' . implode('\',' . '\'', $records) . '\'';
	$query   = "INSERT INTO " . SYSTEM_SETTINGS['database']['info']['db_table'] . " ($columns) VALUES ($records)";
	$result  = mysqli_query($connection, $query);

	!$result ? exit('Failed! ' . mysqli_error($connection)) : true;
}
