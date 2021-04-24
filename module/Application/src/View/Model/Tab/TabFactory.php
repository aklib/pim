<?php

    /**
     *
     * TabFactory.php
     *
     * @since 05.06.2017
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\View\Model\Tab;

    use Application\Utils\ClassUtils;
    use Exception;
    use InvalidArgumentException;
    use Laminas\Form\Fieldset;
    use Laminas\Form\Form;
    use Laminas\View\Model\ModelInterface;
    use Laminas\View\Model\ViewModel;

    class TabFactory
    {
        public const ORIENTATION_VERTICAL = 'vertical';
        public const ORIENTATION_HORIZONTAL = 'horizontal';

        /**
         *
         * @var int
         */
        private static int $countPool = 1;

        /**
         * Tab container (pool)
         * @var TabContainer
         */
        private static ?TabContainer $container = null;

        /**
         * Adds Tab with their content
         * @param array $options
         * @param TabContainer|null $parent
         * @return TabItem
         * @throws Exception
         */
        public static function addTab(array $options, TabContainer $parent = null): TabItem
        {
            $opt = $options;
            $c = $parent ?? self::getContainer($opt);
            if (empty($opt['title'])) {
                throw new InvalidArgumentException(sprintf(
                    '%s expects parameter "label" to be not empty string, "%s" given', __METHOD__, ClassUtils::getName($opt['title'])
                ));
            }

            $tabItem = new TabItem();
            $order = empty($opt['sortOrder']) ? 0 : (int)$opt['sortOrder'];
            $tabItem->setSortOrder($order);
            $tabItem->setResource($opt['resource'] ?? null);
            $tabItem->setPrivilege($opt['privilege'] ?? null);

            $useAcl = (bool)($opt['useAcl'] ?? false);
            $tabItem->setUseAcl($useAcl);
            if (!$tabItem->isAllowed(null, $opt['resource'] ?? null, $opt['privilege'] ?? null)) {
                return $tabItem;
            }
            $tabItem->setRole($opt['role'] ?? null);
            //allowed
            $c->addTabItem($tabItem);
            $tabItem->setTitle($opt['title']);
            $model = new ViewModel();
            $model->setTemplate('default/custom.phtml');
            $tabItem->setContent($model);
            if (!empty($opt['content'])) {
                if ($opt['content'] instanceof ModelInterface) {
                    $tabItem->setContent($opt['content']);
                }
                elseif ($opt['content'] instanceof Fieldset) {
                    $model->setVariable('form', $opt['content']);
                    $model->setTemplate('default/dataForm.phtml');
                }
                elseif (is_array($opt['content'])) {
                    if (!empty($opt['template'])) {
                        $model->setTemplate($opt['template']);
                    }
                    unset($opt['content']['template']);
                    $model->setVariables($opt['content']);
                }
                else {
                    $model->setVariable('content', $opt['content']);
                }
            }
            return $tabItem;
        }

        /**
         * Gets or creates tab container. It's can given more containers
         * @param array $options
         * @return TabContainer
         */
        private static function getContainer(&$options = []): TabContainer
        {
            if (is_null(self::$container)) {
                $container = new TabContainer();
                $prefix = empty($options['id']) ? 'tabPool_' . self::$countPool++ : $options['id'];
                $container->setHref($prefix);
                unset($options['id']);
                self::$container = $container;
            }
            return self::$container;
        }

        public static function sort($recursively = false): void
        {
            self::getContainer()->sort($recursively);
        }

        /**
         * @return ViewModel
         */
        public static function getViewModel(): ViewModel
        {
            self::sort(true);
            return self::getContainer()->getViewModel();
        }

        public static function setViewModel(ViewModel $viewModel): void
        {
            self::getContainer()->setViewModel($viewModel);
        }

        public static function close(): void
        {
            self::$container = null;
            self::$countPool = 1;
        }

        /** @noinspection PhpUnused */
        public static function addContainerClass(string $class): void
        {
            self::getContainer()->addClass($class);
        }

        /** @noinspection PhpUnused */
        public static function setOrientation(string $orientation): void
        {
            switch ($orientation) {
                case self::ORIENTATION_VERTICAL:
                case self::ORIENTATION_HORIZONTAL:
                    self::getContainer()->setOrientation($orientation);
            }
        }
    }
