<?php /** @noinspection PhpUnused */

    /**
     * Class Options
     * @package Application
     * since: 30.06.2020
     * author: alexej@kisselev.de
     */

    namespace Application;

    use Application\Options\AbstractModuleOptions;

    class ModuleOptions extends AbstractModuleOptions
    {
        protected string $name;
        protected string $shortName;
        protected string $vendor;
        protected string $uploadBasePath;
        protected string $publicPath;
        protected ?bool $displayExceptions = null;

        /**
         * @return string
         */
        public function getName(): string
        {
            return $this->name;
        }

        /**
         * @param string $name
         * @return ModuleOptions
         */
        public function setName(string $name): ModuleOptions
        {
            $this->name = $name;
            return $this;
        }

        /**
         * @return string
         */
        public function getShortName(): string
        {
            return $this->shortName;
        }

        /**
         * @param string $shortName
         * @return ModuleOptions
         */
        public function setShortName(string $shortName): ModuleOptions
        {
            $this->shortName = $shortName;
            return $this;
        }

        /**
         * @return string
         */
        public function getVendor(): string
        {
            return $this->vendor;
        }

        /**
         * @param string $vendor
         * @return ModuleOptions
         */
        public function setVendor(string $vendor): ModuleOptions
        {
            $this->vendor = $vendor;
            return $this;
        }

        /**
         * @return bool
         */
        public function isDisplayExceptions(): bool
        {
            if ($this->displayExceptions === null) {
                $this->displayExceptions = $this->getServiceManager()->get('Config')['view_manager']['display_exceptions'];
            }
            return $this->displayExceptions;
        }

        /**
         * @return string
         */
        public function getUploadBasePath(): string
        {
            return $this->uploadBasePath;
        }

        /**
         * @param string $uploadBasePath
         */
        public function setUploadBasePath(string $uploadBasePath): void
        {
            $this->uploadBasePath = $uploadBasePath;
        }

        /**
         * @return string
         */
        public function getPublicPath(): string
        {
            return $this->publicPath;
        }

        /**
         * @param string $publicPath
         */
        public function setPublicPath(string $publicPath): void
        {
            $this->publicPath = $publicPath;
        }
    }
