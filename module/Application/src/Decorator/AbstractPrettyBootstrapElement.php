<?php
    /**
     * Class AbstractPrettyFields
     * @package Application\Decorator
     *
     * since: 05.08.2020
     * author: alexej@kisselev.de
     */

    namespace Application\Decorator;


    use Application\Entity\AppStatus;

    class AbstractPrettyBootstrapElement extends AbstractAwareDecorator
    {
        /**
         * Creates a HTML element
         * @return string
         */
        /*public function getCreated(): string
        {
            $output = parent::getCreated();
            $options = $this->getUserOptions();
            if (!empty($options[$this->getObject()->getCreateId()])) {
                $output .= ' <small class="text-muted font-italic">(' . $options[$this->getObject()->getCreateId()] . ')</small>';
            }
            return $output;
        }*/

        /**
         * Creates a HTML element
         * @return string
         */
       /* public function getChanged(): string
        {
            $output = parent::getCreated();
            $options = $this->getUserOptions();
            if (!empty($options[$this->getObject()->getChangeId()])) {
                $output .= '<small class="text-muted font-italic">(' . $options[$this->getObject()->getChangeId()] . ')</small>';
            }
            return $output;
        }*/

        /**
         * Creates a HTML element
         * @return string
         */
        public function getStatus(): string
        {
            switch (parent::getStatus()) {
                case 'inactive':
                    $class = 'disabled';
                    $name = 'Disabled';
                    $char = 'D';
                    break;
                case 'active':
                    $class = 'success';
                    $name = 'Active';
                    $char = 'A';
                    break;
                case 'new':
                    $class = 'warning';
                    $name = 'New';
                    $char = 'N';
                    break;
                default:
                    $class = 'secondary';
                    $name = 'not set';
                    $char = '0';
            }
            $pattern = '<span title="%s" class="label label-light-%s">%s</span>';
            return sprintf($pattern, $name, $class, $char);
        }

    }