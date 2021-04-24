<?php

/**
 * 
 * Module.php
 * 
 * @since 31.03.2018
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Metronic;

class Module {
    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }
}
