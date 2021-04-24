<?php /** @noinspection ContractViolationInspection */

namespace Report;

use Application\Options\AbstractModuleOptions;

class ModuleOptions extends AbstractModuleOptions
{
    protected array $activateOnRoutes = [];

    /**
     * @return array
     */
    public function getActivateOnRoutes(): array
    {
        return $this->activateOnRoutes;
    }

    /**
     * @param array $activateOnRoutes
     */
    public function setActivateOnRoutes(array $activateOnRoutes): void
    {
        $this->activateOnRoutes = $activateOnRoutes;
    }

}
