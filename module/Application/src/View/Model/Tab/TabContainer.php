<?php /** @noinspection PhpUnused */

    /**
     *
     * Class TabContainer
     *
     * @since 21.07.2020
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\View\Model\Tab;

    use Application\Permission\AbstractAclAwareContainer;
    use Exception;
    use Laminas\View\Model\ViewModel;
    use RuntimeException;

    class TabContainer extends AbstractAclAwareContainer
    {

        private array $tabs = [];
        private ?ViewModel $viewModel = null;
        private string $href;
        private string $orientation = 'horizontal';
        private array $class = [];

        /**
         * Returns a tab by id
         * @param int $id
         * @return TabItem
         */
        public function getTabItem($id): TabItem
        {
            return $this->tabs[(int)$id];
        }

        /**
         * Add a Tab
         *
         * @param TabItem $item
         * @return self
         * @throws Exception
         */
        public function addTabItem(TabItem $item): self
        {
            if ($item === $this) {
                throw new RuntimeException("Can't add self as subtab");
            }
            $this->tabs[] = $item;
            $item->setParent($this);
            if ($item->getSortOrder() === 0) {
                $item->setSortOrder(count($this->tabs));
            }
            return $this;
        }

        /**
         * @return string
         */
        public function getHref(): string
        {
            if ($this->href === null) {
                try {
                    $this->href = 'tab' . random_int(1, 100);
                } catch (Exception $e) {
                }
            }
            return $this->href;
        }

        public function setHref($href): self
        {
            $this->href = $href;
            return $this;
        }

        /**
         *
         * @return boolean
         */
        public function hasTabs(): bool
        {
            return count($this->tabs) > 0;
        }

        /**
         *
         * @return array tabs
         */
        public function getTabs(): array
        {
            return $this->tabs;
        }

        public function getCount(): int
        {
            return count($this->tabs);
        }

        /**
         * sorts tabs by sort id
         * @param bool $recursively
         * @return TabItem
         */
        public function sort(bool $recursively = false): self
        {
            if (!$this->hasTabs()) {
                return $this;
            }
            usort($this->tabs, static function ($a, $b) {
                return $a->getSortOrder() > $b->getSortOrder();
            });
            if ($recursively) {
                foreach ($this->tabs as $tab) {
                    $tab->sort(true);
                }
            }
            return $this;
        }

        /**
         * @return ViewModel
         */
        public function getViewModel(): ViewModel
        {
            if ($this->viewModel === null) {
                $viewModel = new ViewModel();
                $this->viewModel = $viewModel;
            } else {
                $viewModel = $this->viewModel;
            }
            $this->initViewModel($viewModel);
            return $viewModel;
        }

        private function initViewModel(ViewModel $viewModel): void
        {
            $viewModel->setVariable('container', $this);
            $template = $this->orientation === 'vertical' ? 'default/tab/flex-vertical' : 'default/tab/flex-horizontal';
            $viewModel->setTemplate($template);
        }

        public function setViewModel(ViewModel $viewModel): void
        {
            $this->viewModel = $viewModel;
            $this->initViewModel($viewModel);
        }

        /**
         * @return string
         */
        public function getOrientation(): string
        {
            return $this->orientation;
        }

        /**
         * @param string $orientation
         * @return TabContainer
         */
        public function setOrientation(string $orientation): self
        {
            $this->orientation = $orientation;
            return $this;
        }

        /**
         * @return string
         */
        public function getClass(): string
        {
            return implode(' ', array_unique($this->class));
        }

        /**
         * @param string $class
         * @return TabContainer
         */
        public function setClass(string $class): self
        {
            $this->class = [$class];
            return $this;
        }

        /**
         * @param string $class
         * @param bool $recursively
         * @return TabContainer
         */
        public function addClass(string $class, bool $recursively = false): self
        {
            $this->class[] = $class;
            if ($recursively) {
                /** @var TabContainer $tab */
                foreach ($this->tabs as $tab) {
                    $tab->addClass($class, true);
                }
            }
            return $this;
        }


    }
