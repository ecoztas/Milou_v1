<?php header('Content-Type: text/html; charset=utf-8');
# ##############################################################################
# PACKAGE:		Milou_v1
# AUTHOR		Emre Can ÖZTAŞ <me@emrecanoztas.com>
# COPYRIGHT		Copyright (c) 2018, Emre Can ÖZTAŞ. (https://emrecanoztas.com/)
# LICENSE		http://opensource.org/licenses/MIT  MIT License
# LINK			https://github.com/ecoztas/Milou_v1
# SINCE			Version 1.0.0
# ##############################################################################
# ------------------------------------------------------------------------------
# CONTROL: cURL AND CLI
# ------------------------------------------------------------------------------
if (!function_exists('curl_version')) {
	trigger_error('cURL is not found!');
	exit();
} else {
	if (php_sapi_name() !== 'cli') {
		trigger_error('This is for CLI programmers not for browser boys!');
		exit();
	}
}
# ------------------------------------------------------------------------------
# SETTING: PHP.INI
# ------------------------------------------------------------------------------
@ini_set('default_charset', 'utf-8');
@ini_set('memory_limit', '1024M');
@ini_set('file_uploads', 1);
@ini_set('max_execution_time', -1);
@ini_set('max_input_time', -1);
@ini_set('upload_max_filesize', '5M');
@ini_set('session.gc_maxlifetime', 14400);
@ini_set('date.timezone', 'Europe/Istanbul');
@ini_set('expose_php', -1);
@ini_set('allow_url_fopen', -1);
@ini_set('allow_url_include', -1);
gc_enable();
# ------------------------------------------------------------------------------
# DEFINE: SYSTEM CONSTANTS
# ------------------------------------------------------------------------------
defined('DIRECTORY_SEPERATOR') or define('DIRECTORY_SEPERATOR', '/');
defined('ROOT_PATH') or define('ROOT_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPERATOR);
# ------------------------------------------------------------------------------
# DEFINE: DEBUG MODE
# ------------------------------------------------------------------------------
defined('DEBUG_MODE') or define('DEBUG_MODE', TRUE);
# ------------------------------------------------------------------------------
# CONTROL: DEBUG_MODE
# ------------------------------------------------------------------------------
if(DEBUG_MODE){
    @ini_set('display_error', 1);
    error_reporting(E_ALL);
    libxml_use_internal_errors(false);
} else {
    @ini_set('display_error', -1);
    error_reporting(-1);
    libxml_use_internal_errors(true);
}
# ------------------------------------------------------------------------------
# DEFINE: USER AGENT
# ------------------------------------------------------------------------------
const USER_AGENT  = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
# ------------------------------------------------------------------------------
# DEFINE: HTTP HEADER
# ------------------------------------------------------------------------------
const HTTP_HEADER = array (
    'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
    'Cache-Control: max-age=0',
    'Connection: keep-alive',
    'Keep-Alive: 300',
    'Accept-Charset: ISO-8859-9,utf-8;q=0.7,*;q=0.7',
    'Accept-Language: en-us,en;q=0.5',
    'Pragma: '
);
# ------------------------------------------------------------------------------
# DEFINE: SYSTEM SETTINGS
# ------------------------------------------------------------------------------
const SYSTEM_SETTINGS = array(
    'data' => array(
        'data_file'       => 'data.txt',
        'empty_data_name' => 'None'
    ),
    'database' => array(
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'db_name'  => 'db_data',
        'db_table' => 'tbl_data',
        'schema' => array(
			'name',
			'surname',
			'age'
        ),
        'charset'   => 'UTF8',
        'collation' => 'utf8_turkish_ci'
    )
);
# ------------------------------------------------------------------------------
# :::::::::::::::::::::::::::::::::MAIN BLOCK:::::::::::::::::::::::::::::::::::
# ------------------------------------------------------------------------------
$page_details 	= array();
$data_file 		= ROOT_PATH . DIRECTORY_SEPERATOR . SYSTEM_SETTINGS['data']['data_file'];
if (file_exists($data_file)) {
	$page = file_get_contents($data_file);
	if (!empty($page)) {
        $page_details = array_filter(array_map('trim', explode(',', $page)), function($page_line) {
            if (isset($page_line)) {
                return($page_line);
            }
        });
        $base_url = $page_details[0];
        if (filter_var($base_url, FILTER_VALIDATE_URL)) {
            $const_url = parse_url($base_url)['scheme'] . '://' . parse_url($base_url)['host'];
			array_shift($page_details);
			crawler($base_url, $const_url); // start
        } else {
        	exit('$base_url is not URL!');
        }
	} else {
		exit('Data file is empty!');
	}
} else {
	exit('Data file is not found!');
}
# ------------------------------------------------------------------------------
# FUNCTION: CRAWLER
# ------------------------------------------------------------------------------
function crawler($base_url, $const_url)
{
	global $page_details;
	
	static $page_number = 0;
	static $found_url   = array();
	static $visited_url = array();

	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL            => $base_url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HEADER         => false,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_ENCODING       => '',
		CURLOPT_USERAGENT      => USER_AGENT,
		CURLOPT_HTTPHEADER     => HTTP_HEADER,
		CURLOPT_AUTOREFERER    => true,
		CURLOPT_CONNECTTIMEOUT => 120,
		CURLOPT_TIMEOUT        => 120,
		CURLOPT_MAXREDIRS      => 10,
		CURLOPT_SSL_VERIFYPEER => false
	));
	$html = curl_exec($curl);
	curl_close($curl);
	unset($curl);

	$document = new DOMDocument();
	@$document->loadHTML($html);

	$a_list = $document->getElementsByTagName('a');

	if (is_object($a_list)) {
		foreach ($a_list as $a) {
			$a_href = $a->getAttribute('href');

			if (substr($a_href, 0, 1) == "/" && substr($a_href, 0, 2) != "//") {
				$a_href = @parse_url($base_url)["scheme"] . "://" . @parse_url($base_url)["host"] . $a_href;
			} else if (substr($a_href, 0, 2) == "//") {
				$a_href = @parse_url($base_url)["scheme"] . ":" . $a_href;
			} else if (substr($a_href, 0, 2) == "./") {
				$a_href = @parse_url($base_url)["scheme"] . "://" . @parse_url($base_url)["host"] . dirname(@parse_url($base_url)["path"]).substr($a_href, 1);
			} else if (substr($a_href, 0, 1) == "#") {
				$a_href = @parse_url($base_url)["scheme"] . "://" . @parse_url($base_url)["host"] . @parse_url($base_url)["path"] . $a_href;
				continue;
			} else if (substr($a_href, 0, 3) == "../") {
				$a_href = @parse_url($base_url)["scheme"]."://".@parse_url($base_url)["host"]."/".$a_href;
			} else if (substr($a_href, 0, 11) == "javascript:") {
				continue;
			} else if (substr($a_href, 0, 5) != "https" && substr($a_href, 0, 4) != "http") {
				$a_href = @parse_url($base_url)["scheme"] . "://" . @parse_url($base_url)["host"] . "/" . $a_href;
			}

			if (!in_array($a_href, $visited_url)) {
				$parse_url = @parse_url($a_href)['scheme'] . '://' . @parse_url($a_href)['host'];
				if ($const_url === $parse_url) {
					$found_url[]   = $a_href;
					$visited_url[] = $a_href;

					echo(PHP_EOL . $page_number . ' - ' . $a_href . PHP_EOL);
					$page_number++;
					
					!(empty($page_details)) ? scraper($a_href) : false; // Run scraper
				}
			}
		}
	}

	array_shift($found_url);

	if (count($found_url) > 0) {
		foreach ($found_url as $base_url) {
			crawler($base_url, $const_url);
		}	
	} else {
		echo(PHP_EOL);
		echo('Crawling finished');
		echo(PHP_EOL);
	}
}
# ------------------------------------------------------------------------------
# FUNCTION: SCRAPER
# ------------------------------------------------------------------------------
function scraper($base_url)
{
	global $page_details;
	$page_content = array();

	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL            => $base_url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HEADER         => false,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_ENCODING       => '',
		CURLOPT_USERAGENT      => USER_AGENT,
		CURLOPT_HTTPHEADER     => HTTP_HEADER,
		CURLOPT_AUTOREFERER    => true,
		CURLOPT_CONNECTTIMEOUT => 120,
		CURLOPT_TIMEOUT        => 120,
		CURLOPT_MAXREDIRS      => 10,
		CURLOPT_SSL_VERIFYPEER => false
	));
	$html = curl_exec($curl);
	curl_close($curl);
	unset($curl);

    $page_encoding = strtolower(mb_detect_encoding($html));
    $page_encoding != 'utf-8' ? $html = mb_convert_encoding($html, 'ISO-8859-1', 'utf-8') : null;

	$document = new DOMDocument();
	@$document->loadHTML($html);
	$xpath = new DOMXPath($document);

	$is_first_data_exist = @$xpath->query(trim($page_details[0]))->item(0)->textContent;

	if (!empty($is_first_data_exist)) {
		foreach ($page_details as $detail) {
			if ($detail === 'null' || $detail === null) {
				array_push($page_content, SYSTEM_SETTINGS['data']['empty_data_name']);
			} else {
				$data = (string)trim(@$xpath->query(trim($detail))->item(0)->textContent);
				$data = sanitize($data);
				!empty(trim($data)) ? array_push($page_content, $data) : array_push($page_content, SYSTEM_SETTINGS['data']['empty_data_name']);		
			}
		}

		// Generate unique id
		$generate_uniq_id 	= mb_substr(str_shuffle(strtoupper(md5(uniqid(rand(), true)))), 0, 10);
		$fetching_time 		= date('Y-m-d');
		
		array_unshift($page_content, $generate_uniq_id);
		array_push($page_content, $fetching_time);
		array_push($page_content, $base_url);
		print_r($page_content);
		
		database($page_content);
	}
}
# ------------------------------------------------------------------------------
# FUNCTION: SANITIZE
# ------------------------------------------------------------------------------
function sanitize($input)
{
	$sanitize_rules = array(
		'@<script[^>]*?>.*?</script>@si', 
		'@<[\/\!]*?[^<>]*?>@si',
		'@<style[^>]*?>.*?</style>@siU',
		'@<![\s\S]*?--[ \t\n\r]*>@'
	);
	$data = preg_replace($sanitize_rules, '', trim($input));
	
	return($data);
}
# ------------------------------------------------------------------------------
# FUNCTION: DATABASE
# ------------------------------------------------------------------------------
function database($records)
{
	static $connection = null;

	if (!@mysqli_ping($connection)) {
		$connection = mysqli_connect(
			SYSTEM_SETTINGS['database']['hostname'],
			SYSTEM_SETTINGS['database']['username'],
			SYSTEM_SETTINGS['database']['password'],
			SYSTEM_SETTINGS['database']['db_name']
		);

		if (mysqli_connect_errno()) {
			exit('Connection is failed! ' . mysqli_error($connection));
		} else {
			mysqli_set_charset($connection, SYSTEM_SETTINGS['database']['charset']);
			mysqli_query($connection, "SET NAMES "  . SYSTEM_SETTINGS['database']['charset']);
			mysqli_query($connection, "SET SESSION collation_connection=" . SYSTEM_SETTINGS['database']['collation']);
		}		
	}

	$columns = implode(', ', SYSTEM_SETTINGS['database']['schema']);
	$records = '\'' . implode('\',' . '\'', $records) . '\'';
	$query   = "INSERT INTO " . SYSTEM_SETTINGS['database']['db_table'] . " ($columns) VALUES ($records)";
	$result  = mysqli_query($connection, $query);

	if (!result) {
		trigger_error('Failed ' . mysqli_error($connection));
		exit();
	}
}