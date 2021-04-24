<?php

namespace User\Controller;

use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Email\Message\Message;
use Email\Service\EmailManager;
use Exception;
use Laminas\Authentication\Result;
use Laminas\Http\Response;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use User\Entity\User;
use User\Form\LoginForm;
use User\Form\UserPasswordChange;
use User\Form\UserPasswordReset;
use User\Form\UserPasswordSet;
use User\Service\AuthManager;
use User\Service\UserManager;

/**
 * This controller is responsible for letting the user to log in and log out.
 */
class AuthController extends AbstractUserController
{
    public function onDispatch(MvcEvent $e)
    {
        $this->layout('layout/login');
        return parent::onDispatch($e);
    }

    /**
     * @return Response|ViewModel|null
     * @noinspection PhpUnused
     */
    public function loginAction()
    {
        /*if ($this->getServiceManager()->get(AuthenticationService::class)->hasIdentity()) {
            $this->getServiceManager()->get(AuthManager::class)->logout();
        }*/
        // Create login form
        /** @var LoginForm $form */
        $form = $this->getServiceManager()->get(LoginForm::class);
        $form->createForm();

        // Store login status.
        $isLoginError = false;
        $viewModel = new ViewModel([
            'form'         => $form,
            'isLoginError' => $isLoginError
        ]);
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            // Fill in the form with POST data
            $data = $this->params()->fromPost();
            $form->setData($data);
            // Validate form
            if ($form->isValid()) {
                // Get filtered and validated data
                $data = $form->getData();
                // Perform login attempt.
                $result = $this->getServiceManager()->get(AuthManager::class)->login($data['email'],
                    $data['password'], $data['remember_me']);
                // Check result.
                if ($result->getCode() === Result::SUCCESS) {
                    $url = $this->getURL();
                    return $this->redirect()->toUrl($url);
                }
                $viewModel->setVariable('isLoginError', true);
            } else {
                $viewModel->setVariable('isLoginError', true);
            }
        }
        if ($this->layout()->getVariable('message')) {
            //  message from forward
            $viewModel->setVariable('message', $this->layout()->getVariable('message'));
            $this->layout()->setVariable('message', null);
        }
        return $viewModel;
    }

    /**
     * @return Response
     * @noinspection PhpUnused
     */
    public function logoutAction(): Response
    {
        /** @var AuthManager $authManager */
        $authManager = $this->getServiceManager()->get(AuthManager::class);
        $authManager->logout();
        $url = $this->getURL();
        return $this->redirect()->toUrl($url);
    }

    /**
     * @return Response|ViewModel|null
     * @throws ORMException
     * @throws OptimisticLockException
     * @noinspection PhpUnused
     */
    public function resetPasswordAction()
    {
        /** @var UserPasswordReset $createForm */
        $form = $this->getServiceManager()->get(UserPasswordReset::class);
        $form->createForm();
        $viewModel = new ViewModel([
            'form' => $form
        ]);
        if ($this->getRequest()->isPost()) {
            // Fill in the form with POST data
            $data = $this->params()->fromPost();
            $form->setData($data);
            // Validate form
            if ($form->isValid()) {
                // Get filtered and validated data
                $email = $form->getData()['email'];
                /** @var User $user */
                /** @noinspection PhpUndefinedMethodInspection */
                $user = $this->getEntityManager()->getRepository(User::class)->findOneByEmail($email);
                if ($user !== null && $user->getStatus()->getId() === User::STATUS_ACTIVE) {
                    // Generate a new password for user and send an E-mail
                    // notification about that.
                    /** @var UserManager $userManager */
                    $userManager = $this->getServiceManager()->get(UserManager::class);
                    // generate token and save it for user into DB
                    $token = $userManager->generatePasswordResetToken($user);
                    $url = $this->url()->fromRoute('auth', ['action' => 'setPassword'], ['force_canonical' => true, 'query' => ['token' => $token, 'email' => $email]]);
                    /*echo $url;
                    die;*/
                    /** @var EmailManager $manager */
                    $manager = $this->getServiceManager()->get(EmailManager::class);
                    try {
                        $manager
                            ->setTemplate('email/password/recover')
                            ->setSubject($this->translate('Roi Push recover password'))
                            ->addTo($user->getEmail(), $user->getFirstName())
                            ->addTemplateVar('url', $url)
                            ->send();
                    } catch (Exception $e) {
                        $message = $this->translate('#generic: email error message');
                        $this->addMessage($message, FlashMessenger::NAMESPACE_ERROR);
                        return $this->redirect()->toRoute('auth', ['action' => 'resetPassword']);
                    }
                    // Success. Redirect to "message" page
                    return $this->redirect()->toRoute('auth', ['action' => 'message']);
                }
                $this->addMessage(sprintf('User with email "%s" not found', $this->escapeHtml($email)), FlashMessenger::NAMESPACE_ERROR);
                return $this->redirect()->toRoute('auth', ['action' => 'resetPassword']);
            }
        }
        return $viewModel;
    }

    /**
     * This action displays the "Reset Password" page.
     * @return Response|ViewModel|null
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function setPasswordAction()
    {
        $token = $this->params()->fromQuery('token', null);
        $email = $this->params()->fromQuery('email', null);

        // Validate token length
        if ($token !== null && (!is_string($token) || strlen($token) !== 32)) {
            $this->addMessage('Invalid token', FlashMessenger::NAMESPACE_ERROR);
            return $this->redirect()->toRoute('auth', ['action' => 'resetPassword']);
        }

        /** @var User $user */
        $user = $this->getEntityManager()->getRepository(User::class)->findOneByEmail($email);
        if ($user === null) {
            $this->addMessage(sprintf('User with email "%s" not found', $this->escapeHtml($email)), FlashMessenger::NAMESPACE_ERROR);
            return $this->redirect()->toRoute('auth', ['action' => 'resetPassword']);
        }
        /** @var UserManager $userManager */
        $userManager = $this->getServiceManager()->get(UserManager::class);

        if ($token === null || !$userManager->validatePasswordResetToken($user, $token)) {
            $this->addMessage('The url has been expired. Please try again.', FlashMessenger::NAMESPACE_ERROR);
            return $this->redirect()->toRoute('auth', ['action' => 'resetPassword']);
        }

        // Create form
        /** @var UserPasswordChange $form */
        $form = $this->getServiceManager()->get(UserPasswordSet::class);
        $form->createForm();
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            // Fill in the form with POST data
            $data = $this->params()->fromPost();
            $form->setData($data);
            // Validate form
            if ($form->isValid()) {
                $data = $this->postValidateFormData($data, $user);
                $user->setPassword($data['password']);
                $user->setPasswordResetToken(null);
                $user->setPasswordResetTokenCreationDate(null);
                $user->setChanged(new DateTime());
                $user->setChangeId($user->getId());

                $this->getEntityManager()->flush($user);
                $this->addMessage('The pasword has been set', FlashMessenger::NAMESPACE_SUCCESS);
                return $this->redirect()->toRoute('auth', ['action' => 'login']);
            }
        }
        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * @return array
     * @noinspection PhpUnused
     */
    public function messageAction(): array
    {
        return [];
    }
}
