<?php /** @noinspection ContractViolationInspection */

namespace Elastic;

use Application\Options\AbstractModuleOptions;

class ModuleOptions extends AbstractModuleOptions
{

    private array $connections;

    /**
     * @return array
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * @param array $connections
     */
    public function setConnections(array $connections): void
    {
        $this->connections = $connections;
    }
}
