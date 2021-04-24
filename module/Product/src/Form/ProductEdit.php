<?php
/**
 * Class ProductEdit
 * @package Product\Form
 *
 * since: 15.09.2020
 * author: alexej@kisselev.de
 */

namespace Product\Form;

use Application\Form\EntityEdit;
use Application\View\Helper\Form\FormFile;
use Application\View\Manager\ViewManager;
use Laminas\Form\Element\File;
use Laminas\Form\ElementInterface;
use Laminas\Form\Fieldset;
use Product\Entity\Product;
use Product\Entity\ProductCreative;
use Product\ModuleOptions;

class ProductEdit extends EntityEdit
{
    protected function postCreateElement(ElementInterface $element): void
    {
        parent::postCreateElement($element);

        if ($element instanceof Fieldset && $element->getName() === 'creative' && $element->has('creative[creativeFiles]')) {
            /** @var FormFile $helper */
            $helper = $this->getServiceManager()->get('ViewHelperManager')->get('formFile');
            if (!$helper instanceof FormFile) {
                return;
            }
            $helper->setTemplate('form/creative/file');
            // remove legend
            $element->setLabel('');
            $fileElement = $element->get('creative[creativeFiles]');
            $fileElement->removeAttribute('multiple');

            /** @var ModuleOptions $moduleOptions */
            $moduleOptions = $this->getServiceManager()->get(ModuleOptions::class);
            $accept = $moduleOptions->getUploadAccept();
            if (isset($accept['creativeFiles'])) {
                /** @var File $fileElement */
                $fileElement->setAttribute('accept', $accept['creativeFiles']);
            }
        }
    }

    protected function isPropertyShown($name): bool
    {
        if ($this->_entity instanceof ProductCreative) {
            /** @var ViewManager $viewManager */
            $viewManager = $this->getServiceManager()->get(ViewManager::class);
            $controllerName = $viewManager->getControllerName();
            if ($this->getAcl()->isAllowedColumn(null, $controllerName, 'creative')) {
                return $name !== 'id';
            }
        }

        if ($this->_entity instanceof Product) {
            switch ($name) {
                case 'feed':
                case 'Feed':
                    return $this->_entity->getAdvertiser()->isExternal();
                case 'creative':
                    return !$this->_entity->getAdvertiser()->isExternal();
            }
        }

        return parent::isPropertyShown($name);
    }
}
