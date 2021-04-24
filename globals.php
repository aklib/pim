<?php

/**
 *
 * globals.php
 *
 * @since 30.06.2020
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */


defined('APPLICATION_PATH') || define('APPLICATION_PATH', __DIR__);
$isCli = strpos(PHP_SAPI, 'cli') === 0;

if (!$isCli) {
    defined('HTTP_HOST')
    || define('HTTP_HOST', !empty($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] :
        $_SERVER['HTTP_HOST']);
}


define('URL_VALUE_SEPARATOR', '|');

function dump($var)
{
    if (empty($_SERVER['REMOTE_ADDR'])) {
        return;
    }
    if (substr($_SERVER['REMOTE_ADDR'], 0, 8) == '192.168.') {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        for ($i = 0; $i < $numargs; $i++) {
            echo '<div style="clear:both;text-align:left;z-index:99999999;"><pre><xmp style="font-size:11px;font-family:Arial,Verdana;">' . print_r($arg_list[$i],
                    10) . '</xmp></pre></div>';
        }
    }
}

function devhelp($fileName)
{
    if (substr($_SERVER['REMOTE_ADDR'], 0, 8) == '192.168.') {
        return ' title="SCRIPT: ' . str_replace(APPLICATION_PATH, '', $fileName) . '"';
    }

}