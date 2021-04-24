<?php /** @noinspection PhpUndefinedFieldInspection */

    /**
 * 
 * History.php
 * 
 * @since 25.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\View\Helper;

use FlorianWolters\Component\Core\StringUtils;
use Laminas\Session\Container;
use Laminas\Session\ManagerInterface;

class History extends AbstractHelperAware {

    /**
     * Default history namespace
     */
    public const NAMESPACE_HISTORY = 'history';

    /**
     * @var ManagerInterface
     */
    protected ?ManagerInterface $session = null;

    /**
     * presents namespace for urls (eg. product/list)
     * @var Container
     */
    protected ?Container $history = null;

    /**
     * list of not saved controller names
     * @var array controller names
     */
    protected array $blackList = [
        'error','auth'
    ];

    protected string $controllerName = '';
    protected string $actionName = '';
    protected string $moduleName = '';

    /**
     * Set the session manager
     *
     * @param  ManagerInterface $manager
     * @return History
     */
    protected function setSessionManager(ManagerInterface $manager): History
    {
        $this->session = $manager;
        return $this;
    }

    /**
     * Retrieve the session manager
     *
     * If none composed, lazy-loads a SessionManager instance
     *
     * @return ManagerInterface
     */
    protected function getSessionManager(): ManagerInterface
    {
        if (!$this->session instanceof ManagerInterface) {
            $this->setSessionManager(Container::getDefaultManager());
        }
        return $this->session;
    }

    /**
     * returns history object
     * @return Container
     */
    protected function getHistory(): ?Container
    {
        if ($this->history === null) {
            $manager = $this->getSessionManager();
            $this->history = new Container(self::NAMESPACE_HISTORY, $manager);
        }
        return $this->history;
    }

    public function setControllerName($name): History
    {
        $this->controllerName = str_replace('controller', '', strtolower(StringUtils::substringAfterLast($name, '\\')));
        return $this;
    }

    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    public function setActionName($name): History
    {
        $this->actionName = strtolower($name);
        return $this;
    }

    public function getActionName(): string
    {
        return $this->actionName;
    }

    public function setModuleName($name): History
    {
        $this->moduleName = strtolower($name);
        return $this;
    }

    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * checks if last and current uri eqial
     * @return bool
     */
    public function isReload() : bool {
        return $this->getHistory()->last === $_SERVER['REQUEST_URI'];
    }

    /**
     * saves an URI referenced to action and as last
     */
    public function save(): History
    {
        if (in_array($this->getControllerName(), $this->blackList, true)) {
            return $this;
        }
        if (!array_key_exists('REQUEST_URI', $_SERVER)) {
            return $this;
        }
        $history = $this->getHistory();
        $uri = $_SERVER['REQUEST_URI'];
        $history->{$this->getControllerName() . $this->getActionName()} = $uri;
        $history->{$this->getModuleName() . $this->getControllerName() . $this->getActionName()} = $uri;
        $history->{$this->getActionName()} = $uri;
        /** @noinspection PhpUndefinedFieldInspection */
        $history->last = $uri;
        return $this;
    }

    /**
     * Returns last URI for specified route data
     * @param string|null $action
     * @param string|null $controller
     * @param string|null $module
     * @return string URI
     * @example $this->_redirect($this->view->history()->getLastUrlFor('list','product'),array('prependBase'=>false));
     */
    public function getLastUrlFor(string $action = null, string $controller = null, string $module = null): ?string {
        $history = $this->getHistory();
        if (empty($action)) {
            return $history->last;
        }
        if (!empty($controller) && !empty($module)) {
            return $history->{$module . $controller . $action};
        }
        //return by controller->action
        if (!empty($controller)) {
            return $history->{$controller . $action};
        }
        //return by action only
        if (!empty($history->{$action})) {
            return $history->{$action};
        }
        //create from current route
        return ''; // Go back to index/index by default;
    }

    /**
     * Returns last URI
     * @example $this->_redirect($this->view->history()->getLastUrl(),array('prependBase'=>false));
     * @return string URI
     */
    public function getLastUrl() : ?string {
        return $this->getHistory()->last;
    }

    public function clear() : void {
        $this->history = null;
    }
}
