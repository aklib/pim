<?php /** @noinspection PhpUnused */

    /**
     *
     * newEmptyPHP.php
     *
     * @since 09.05.2017
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\Decorator;

    use Application\Entity\AppStatus;
    use Application\Repository\StatusDao;
    use Application\ServiceManager\Interfaces\Constant;
    use Application\ServiceManager\Interfaces\TranslatorAware;
    use Application\ServiceManager\Interfaces\ViewRendererAware;
    use DateTime;
    use Laminas\I18n\Translator\TranslatorInterface;
    use Laminas\View\Renderer\PhpRenderer;
    use User\Entity\User;

    abstract class AbstractAwareDecorator extends AbstractEntityDecorator implements ViewRendererAware, TranslatorAware
    {

        private array $userOptions = [];
        private PhpRenderer $phpRenderer;
        private ?TranslatorInterface $translator;

        public function getViewRenderer(): PhpRenderer
        {
            return $this->phpRenderer;
        }

        public function setViewRenderer(PhpRenderer $viewRenderer): void
        {
            $this->phpRenderer = $viewRenderer;
        }

        private function setUserOptions(array $userOptions): void
        {
            $this->userOptions = $userOptions;
        }

        //==================== SHARED FUNCTIONS =======================

        protected function getUserOptions(): array
        {
            if (empty($this->userOptions)) {
                /** @var StatusDao $dao */
                /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                $this->setUserOptions($this->getEntityManager()->getRepository(User::class)->getNamedQueryResult(Constant::NAMED_QUERY_DROPDOWN_CHOICE));
            }
            return $this->userOptions;
        }

        /**
         * Create HTML status presentation or string name of status
         * @return string
         * @noinspection PhpUnused
         */
        public function getStatus(): string
        {
            $id = 0;
            if (method_exists($this->getObject(), 'getStatus')) {
                $id = $this->getObject()->getStatus() instanceof AppStatus ? $this->getObject()->getStatus()->getId() : (int)$this->getObject()->getStatus();
            }
            if (empty($id)) {
                return 'unknown';
            }
            $options = $this->getEntityManager()->getRepository(AppStatus::class)->getNamedQueryResult(Constant::NAMED_QUERY_DROPDOWN_CHOICE);
            return $options[$id];

        }

//==================== DATE FUNCTIONS =======================
        protected string $format = 'd.m.Y H:i:s';

        /**
         *
         * @param DateTime $dateObject
         * @param null $format
         * @return string formatted date
         */
        public function formatDate(DateTime $dateObject, $format = null): string
        {
            if (is_object($dateObject)) {
                //DateTime object
                return $dateObject->format(empty($format) ? $this->format : $format);
            }
            return $dateObject;
        }

        public function getCreated(): string
        {
            if ($this->getObject()->getCreated() === null) {
                return '';
            }
            return $this->formatDate($this->getObject()->getCreated());
        }

        public function getChangeId(): string
        {
            if (method_exists($this->getObject(), 'getChangeId') && isset($this->getUserOptions()[$this->getObject()->getChangeId()])) {
                return $this->getUserOptions()[$this->getObject()->getChangeId()];
            }
            return '';
        }

        public function getCreateId(): string
        {
            if (method_exists($this->getObject(), 'getCreateId') && isset($this->getUserOptions()[$this->getObject()->getCreateId()])) {
                return $this->getUserOptions()[$this->getObject()->getCreateId()];
            }
            return '';
        }

        /**
         * Formats change-date
         * @return string
         */
        public function getChanged(): string
        {
            if ($this->getObject()->getChanged() === null) {
                return '';
            }
            return $this->formatDate($this->getObject()->getChanged());
        }

        /**
         * Formats float|int
         * @param int|string|float $value
         * @param int $decimals
         * @return string
         */
        protected function formatFloat($value, $decimals = 2): string
        {
            return number_format(round((float)$value, $decimals), $decimals, ',', '.');
        }

        public function setTranslator(TranslatorInterface $translator = null): void
        {
            $this->translator = $translator;
        }

        public function translate(string $text = null): string
        {
            if ($text === null || $this->translator === null) {
                return $text;
            }
            return $this->translator->translate($text);
        }
    }
