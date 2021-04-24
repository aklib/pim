<?php /** @noinspection PhpUnused */

/**
 *
 * AuthController.php
 *
 * @since 11.01.2021
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Email\Controller;

use Application\Controller\AbstractModuleController;
use FlorianWolters\Component\Core\StringUtils;
use Laminas\View\Model\ViewModel;

class PreviewController extends AbstractModuleController
{

    public function listAction(ViewModel $viewModel = null, array $options = []): ViewModel
    {
        //parent::listAction();
        if ($viewModel === null) {
            $viewModel = new ViewModel();
        }
        $templateMap = (array)$this->getServiceManager()->get('Config')['view_manager']['template_map'];

        $templates = [];
        foreach (array_keys($templateMap) as $key) {
            if (StringUtils::startsWith($key, "email")) {
                $tmpl = StringUtils::substringAfter($key, '/');
                switch ($tmpl) {
                    case 'html':
                    case 'text':
                    case 'header':
                    case 'footer':
                        continue 2;
                }
                $templates[] = $key;
            }
        }

        $viewModel->setVariable('templates', $templates);
        return $viewModel;
    }

    public function templateAction(): ViewModel
    {
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $name = $this->params()->fromQuery('name');
        $viewModelTemplate = new ViewModel();
        $viewModelTemplate->setTemplate($name);
        $viewModel->setVariable('isPreview', true);
        $viewModel->addChild($viewModelTemplate, 'content');
        return $viewModel;
    }

}
