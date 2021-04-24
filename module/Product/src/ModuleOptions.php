<?php /** @noinspection ContractViolationInspection */

namespace Product;

use Application\Options\AbstractModuleOptions;
use Psr\Container\ContainerInterface;

class ModuleOptions extends AbstractModuleOptions
{
    protected array $uploadAccept = [];
    protected string $uploadPath;

    /**
     * @return array
     */
    public function getUploadAccept(): array
    {
        return $this->uploadAccept;
    }

    /**
     * @param array $uploadAccept
     */
    public function setUploadAccept(array $uploadAccept): void
    {
        $this->uploadAccept = $uploadAccept;
    }

    /**
     * @return string
     */
    public function getUploadPath(): string
    {
        return $this->uploadPath;
    }

    /**
     * @param string $uploadPath
     */
    public function setUploadPath(string $uploadPath): void
    {
        $this->uploadPath = $uploadPath;
    }
}
