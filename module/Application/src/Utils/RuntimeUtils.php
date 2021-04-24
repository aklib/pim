<?php

/**
 *
 * StringUtils.php
 *
 * @since 01.06.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\Utils;

abstract class RuntimeUtils {

    public static function cli() {
        return (bool)preg_match('/^cli/', php_sapi_name());
    }
}
