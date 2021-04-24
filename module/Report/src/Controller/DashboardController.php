<?php /** @noinspection ContractViolationInspection */

namespace Report\Controller;

use Application\Controller\AbstractModuleController;
use Laminas\View\Model\ViewModel;

/**
 * This controller is responsible for user management (adding, editing,
 * viewing users and changing user's own password).
 */
class DashboardController extends AbstractModuleController
{
    public function listAction(ViewModel $viewModel = null, array $options = []): ViewModel
    {
        if ($this->getCurrentUser()->getUserRole()->isRestricted()) {
            return $this->redirect()->toRoute('default/dashboard', ['action' => 'advertiser']);
        }
        return parent::listAction($viewModel, $options);
    }

    public function advertiserAction(ViewModel $viewModel = null, array $options = []): ViewModel
    {
        return parent::listAction($viewModel, $options);
    }

    public function publisherAction(ViewModel $viewModel = null, array $options = []): ViewModel
    {
        return parent::listAction($viewModel, $options);
    }
}
