<?php

/**
 * Module.php
 *
 * @since 11.01.2021
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 * @copyright apagmedia
 */

namespace Email;

class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

}
