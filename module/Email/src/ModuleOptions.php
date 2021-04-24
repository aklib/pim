<?php /** @noinspection PhpUnused */

/** @noinspection ContractViolationInspection */

namespace Email;

use Application\Options\AbstractModuleOptions;

class ModuleOptions extends AbstractModuleOptions
{
    protected string $fromEmail;
    protected string $fromName;
    protected array $smtp;
    protected string $template = '';
    protected array $templateVars = [];

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getTemplateVars(): array
    {
        return $this->templateVars;
    }

    /**
     * @param array $templateVars
     */
    public function setTemplateVars(array $templateVars): void
    {
        $this->templateVars = $templateVars;
    }

    /**
     * @return string
     */
    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    /**
     * @param string $fromEmail
     */
    public function setFromEmail(string $fromEmail): void
    {
        $this->fromEmail = $fromEmail;
    }

    /**
     * @return string
     */
    public function getFromName(): string
    {
        return $this->fromName;
    }

    /**
     * @param string $fromName
     */
    public function setFromName(string $fromName): void
    {
        $this->fromName = $fromName;
    }

    /**
     * @return array
     */
    public function getSmtp(): array
    {
        return $this->smtp;
    }

    /**
     * @param array $smtp
     */
    public function setSmtp(array $smtp): void
    {
        $this->smtp = $smtp;
    }
}
