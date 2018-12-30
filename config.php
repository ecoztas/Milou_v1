<?php
defined('ROOT_PATH') or exit('ROOT_PATH is not found!');

/**
 * @package     Milou_v1
 * @author      Emre Can ÖZTAŞ (ecoz) <oztasemrecan@gmail.com>
 * @copyright   Copyright (c) 2018, Emre Can ÖZTAŞ. (https://emrecanoztas.com/)
 * @license     http://opensource.org/licenses/MIT  MIT License
 * @link        https://github.com/oztasemrecan/Milou_v1
 * @since       Version 1.0.0
 */

// System settings
defined('SYSTEM_SETTINGS') or define('SYSTEM_SETTINGS', array(
    'data' => array(
        'data_file'      	=> 'data',
        'file_extension' 	=> 'txt',
		'empty_data_name' 	=> 'Belirtilmemiş' 
    ),
    'database'       => array(
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'db_name'  => 'db_firma',
        'db_table' => 'tbl_firma',
        'schema'   => array(
            'firma_adi',
            'sektor',
            'adres',
            'sehir',
            'ilce',
            'telefon_1',
            'telefon_2',
            'e_posta',
            'website',
            'f_url' 
            ),
        'charset'  => 'UTF8'
    )
));

// HTTP Header
defined('HTTP_HEADER') or define('HTTP_HEADER', array(
    'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
    'Cache-Control: max-age=0',
    'Connection: keep-alive',
    'Keep-Alive: 300',
    'Accept-Charset: ISO-8859-9,utf-8;q=0.7,*;q=0.7',
    'Accept-Language: en-us,en;q=0.5',
    'Pragma: '
));

// User-Agent
defined('USER_AGENT') or define('USER_AGENT', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');

// URL Regex
defined('URL_FORMAT') or define('URL_FORMAT', '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i');

// Text clearer
defined('TEXT_CLEANER') or define('TEXT_CLEANER', array(
    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
));

// Punctuation characters list
defined('MARKS_LIST') or define('MARKS_LIST', array(
    '+', 
    ',',
    '.',
    '-',
    '\'',
    '"',
    '&',
    '!',
    '?',
    ':',
    ';',
    '#',
    '~',
    '=',
    '/',
    '$',
    '£',
    '^',
    // '(',
    // ')',
    '_',
    '<',
    '>',
    '{',
    '}',
    '«',
    '»',
    '␠',
    '@',
    '·',
    '*',
    '•',
    '°',
    '¡',
    '¿',
    '¬',
    '№',
    '%',
    '|',
    '¶',
    '§',
    '¨',
    '¦',
    '⁂',
    '☞',
    '∴',
    '‽',
    '※',
    '[',
    ']',
    '¤',
    '¢',
    '$',
    '€',
    '£',
    '¥',
    '₩',
    '₪',
    '†',
    '‡',
    '‰',
    '‱',
));