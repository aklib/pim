<?php /** @noinspection PhpUnused */

/**
 * Class ModuleOptions
 * @package Application
 * since: 21.07.2020
 * author: alexej@kisselev.de
 */

namespace Acl;

use Application\Options\AbstractModuleOptions;


class ModuleOptions extends AbstractModuleOptions
{
    private array $exclude = [];

    /**
     * @return array
     */
    public function getExclude(): array
    {
        return $this->exclude;
    }

    /**
     * @param array $exclude
     */
    public function setExclude(array $exclude): void
    {
        $this->exclude = $exclude;
    }
}
