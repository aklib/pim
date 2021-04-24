<?php /** @noinspection PhpUnused */

/**
 *
 * Class Module
 *
 * @since 19.07.2020
 * @author Alexej Kisselev <alexej@kisselev.de>
 */

namespace Acl;

class Module
{
    /**
     * This method returns the path to module.config.php file.
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}

