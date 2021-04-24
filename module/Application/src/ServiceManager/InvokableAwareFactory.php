<?php

    /**
     *
     * InvokableAwareFactory.php
     *
     * Initializes ServiceManager and EntityManager etc. for invocable instance
     *
     * @since 19.12.2019
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\ServiceManager;

    /**
     *
     */

    use Acl\Service\AclService;
    use Application\ModuleOptions;
    use Application\ServiceManager\Interfaces\AclAware;
    use Application\ServiceManager\Interfaces\AuthenticationAware;
    use Application\ServiceManager\Interfaces\EntityManagerAware;
    use Application\ServiceManager\Interfaces\ModuleOptionsAware;
    use Application\ServiceManager\Interfaces\ServiceManagerAware;
    use Application\ServiceManager\Interfaces\TranslatorAware;
    use Application\ServiceManager\Interfaces\ViewRendererAware;
    use Application\Utils\ClassUtils;
    use Doctrine\ORM\EntityManager;
    use Doctrine\ORM\EntityRepository;
    use Laminas\I18n\Translator\TranslatorInterface;
    use Laminas\Permissions\Acl\AclInterface;
    use Laminas\ServiceManager\Factory\FactoryInterface;
    use Laminas\View\Renderer\PhpRenderer;
    use Psr\Container\ContainerInterface;
    use Throwable;
    use User\Entity\User;

    final class InvokableAwareFactory implements FactoryInterface
    {
        private static ContainerInterface $sm;

        /**
         * {@inheritDoc}
         */
        public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
        {
            $instance = (null === $options) ? new $requestedName() : new $requestedName($options);
            self::$sm = $container;
            self::aware($instance);
            //  call init function if exists
            if (method_exists($instance, 'init')) {
                // a factory must return an instance
                try {
                    $instance->init($options);
                }
                catch (Throwable $e){
                    /** @var ModuleOptions $moduleOptions */
                    $moduleOptions = $container->get(ModuleOptions::class);
                    if($moduleOptions->isDisplayExceptions()){
                        echo '<div class="row"></div><pre>' . $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre></div>';
                        die(__CLASS__ . '::'.__METHOD__);
                    }
                }
            }
            return $instance;
        }

        public static function aware(object $instance): void
        {
            $container = self::$sm;

            if ($instance instanceof ServiceManagerAware) {
                $instance->setServiceManager($container);
            }
//            var_dump(get_class($container));die;
            //  set doctrine EntityManager if exists
            if ($instance instanceof EntityManagerAware && !($instance instanceof EntityRepository)) {
                $instance->setEntityManager($container->get(EntityManager::class));
            }

            //  set doctrine User if exists
            if (($instance instanceof AuthenticationAware) && $container->has('authentication')) {
                $identity = $container->get('authentication')->getIdentity();
                if ($identity instanceof User) {
                    $instance->setCurrentUser($identity);
                }
            }
            if (($instance instanceof ModuleOptionsAware) && is_array($container->get('Config')['module_options'])) {
                $config = (array)$container->get('Config')['module_options'];
                $options = $config[strtolower(ClassUtils::getNamespace($instance))] ?? [];
                $instance->setFromArray($options);
            }
            if ($instance instanceof TranslatorAware) {
                if ($container->has('MvcTranslator')) {
                    $instance->setTranslator($container->get('MvcTranslator'));
                } elseif ($container->has(TranslatorInterface::class)) {
                    $instance->setTranslator($container->get(TranslatorInterface::class));
                } elseif ($container->has('Translator')) {
                    $instance->setTranslator($container->get('Translator'));
                }
            }
            if ($instance instanceof ViewRendererAware) {
                $instance->setViewRenderer($container->get(PhpRenderer::class));
            }

            if ($instance instanceof AclAware && $container->has('acl')) {
                $instance->setAcl($container->get('acl'));
            }
        }

    }
