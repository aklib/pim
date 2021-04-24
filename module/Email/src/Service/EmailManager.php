<?php /** @noinspection PhpUnused */

/**
 *
 * EmailService.php
 *
 * @since 11.01.2021
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 * @copyright apagmedia
 */

namespace Email\Service;

use Application\ServiceManager\Interfaces\ServiceManagerAware;
use Email\ModuleOptions;
use Exception;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Sendmail as SendmailTransport;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Part as MimePart;
use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\PhpRenderer;
use Psr\Container\ContainerInterface;
use RuntimeException;

class EmailManager implements ServiceManagerAware
{
    protected ContainerInterface $sm;
    protected Message $message;
    /**
     * @var string Template
     */
    protected string $template;
    /**
     * @var array
     */
    protected array $templateVars = [];
    protected string $subject;

    public function init(): void
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $this->getServiceManager()->get(ModuleOptions::class);
        $email = $moduleOptions->getFromEmail();
        $name = $moduleOptions->getFromName();
        $this->message = $message = new Message();
        $headers = $message->getHeaders();
        $headers->removeHeader('Content-Type');
        $headers->addHeaderLine('From', "$name <$email>")
            ->addHeaderLine('Reply-To', $email)
            ->addHeaderLine('Content-Type', 'text/html; charset=utf-8')
            ->addHeaderLine('MIME-Version', '1.0')
            ->addHeaderLine('Content-Transfer-Encoding', 'quoted-printable');
        // defaults
        $this->subject = $name;
    }

    /**
     * Creates e-mail ViewModel
     * @return MimeMessage
     */
    protected function createContent(): MimeMessage
    {
        $template = $this->getTemplate();
        if (empty($template)) {
            throw new RuntimeException("E-Mail template can't be empty");
        }
        $layout = new ViewModel($this->getTemplateVars());
        $layout->setTemplate('email/layout');
        $viewModel = new ViewModel();
        $viewModel->setTemplate($template);
        $viewModel->setVariables($this->getTemplateVars());
        // create markup
        $content = $this->getRenderer()->render($viewModel);
        $layout->setVariable('content', $content);
        $markup = $this->getRenderer()->render($layout);
        $html = new MimePart($markup);
        $body = new MimeMessage();
        $body->addPart($html);
        return $body;
    }

    /**
     * Gets php view renderer
     * @return PhpRenderer
     */
    protected function getRenderer(): PhpRenderer
    {
        /** @var PhpRenderer $phpRenderer */
        $phpRenderer = $this->getServiceManager()->get(PhpRenderer::class);
        $phpRenderer->setCanRenderTrees(false);
        return $phpRenderer;
    }

    /**
     * Send the constructed email
     *
     * @todo Add from name
     */
    public function send(): bool
    {
        $message = $this->getMessage();
        if ($message->getTo()->count() === 0) {
            throw new RuntimeException("E-Mail recipient list can't be empty");
        }
        $mimeMessage = $this->createContent();
        $message->setBody($mimeMessage);
        $message->setSubject($this->getSubject());

        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $this->getServiceManager()->get(ModuleOptions::class);

        //Send email
        $smtp = $moduleOptions->getSmtp();

        if (!$smtp['active']) {
            try {
                $transport = new SendmailTransport();
                $transport->send($message);
                return true;
            } catch (Exception $e) {
                throw $e;
            }
        }
        // create SMTP transport
        $options = new SmtpOptions($smtp['options']);
        $transport = new SmtpTransport();
        $transport->setOptions($options);

        try {
            $transport->send($message);
        } catch (Exception $e) {
            throw $e;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     * @return EmailManager
     */
    public function setTemplate(string $template): self
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return array
     */
    public function getTemplateVars(): array
    {
        return $this->templateVars;
    }

    public function addTemplateVars(array $templateVars): self
    {
        foreach ($templateVars as $key => $value) {
            $this->addTemplateVar($key, $value);
        }
        return $this;
    }

    public function addTemplateVar(string $key, string $value): self
    {
        $this->templateVars[$key] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return EmailManager
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param string $email
     * @param string|null $name
     * @return self
     */
    public function addTo(string $email, string $name = null): self
    {
        $this->getMessage()->addTo($email, $name);
        return $this;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    public function getServiceManager(): ContainerInterface
    {
        return $this->sm;
    }

    public function setServiceManager(ContainerInterface $sm): void
    {
        $this->sm = $sm;
    }
}
