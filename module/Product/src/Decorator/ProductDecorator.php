<?php /** @noinspection ContractViolationInspection */

    namespace Product\Decorator;

    use Application\Decorator\AbstractPrettyBootstrapElement;
    use Doctrine\Common\Collections\Collection;
    use Product\Entity\Product;
    use Product\Entity\ProductCreative;

    /**
     * @method Product getObject()
     */

    /**
     * @method Product getObject()
     */
    class ProductDecorator extends AbstractPrettyBootstrapElement
    {

        /** @noinspection PhpUndefinedMethodInspection */
        public function getCountry(): string
        {
            $countries = $this->getObject()->getCountry();
            if ($countries === null) {
                return '';
            }
            if ($countries instanceof Collection) {
                $array = [];
                foreach ($countries as $val) {
                    if (method_exists($val, '__toString')) {
                        $array[] = $val->__toString();
                    }
                }
                $value = implode(', ', $array);
                if (count($array) < 11) {
                    return $value;
                }
                return
                    '<div data-toggle="popover" data-content="' . $value . '" class="text-truncate text-wrap overflow-hidden" style="max-width: 150px; height: 3rem;">' .
                    $value .
                    '</div>';
            }
            return '';
        }

        public function getStatistics(): string
        {
            $res = $this->getObject()->getStatistics();
            if (!$res->isOk()) {
                return 'n/a';
            }
            return $this->getViewRenderer()->render('decorator/statistics', ['resultRow' => $this->getObject()->getStatistics()]);
        }

        public function getStatisticsToday(): string
        {
            $res = $this->getObject()->getStatisticsToday();
            if (!$res->isOk()) {
                return 'n/a';
            }
            return $this->getViewRenderer()->render('decorator/statistics', ['resultRow' => $this->getObject()->getStatisticsToday()]);
        }

        public function getAdvertiser(): string
        {
            $a = $this->getObject()->getAdvertiser();
            if ($a !== null) {
                return $a->getName();
            }
            return 'not set';
        }

        public function getDisplayName(): string
        {
            return $this->getObject()->getName() . ' #' . $this->getObject()->getId();
        }
    }
