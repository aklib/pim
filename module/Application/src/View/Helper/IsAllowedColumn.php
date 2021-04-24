<?php

    /**
 * Checks if one column is allowed for current user
 * IsAllowedColumn.php
 * 
 * @since 28.07.2018
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Model\ViewModel;

class IsAllowedColumn extends AbstractHelper {

    public function __construct() {
        $this->view = $this->getView();
    }
    
    /**
     * Example: in view script: if (!$this->isAllowedColumn($column)) {...}
     * @param array $column
     * @return boolean
     */
    public function __invoke(array $column) {
        if (empty($column)) {
            return true;
        }
        /** @var ViewModel $viewModel */
        $viewModel = $this->view;
        //  1. Handle assign method
        if (isset($column['assign'])) {
            $method = $column['assign']['method'];
            $values = (array) $column['assign']['value'];
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            $value = $viewModel->decorate($viewModel->identity(), $method);
            if (!in_array($value, $values, true)) {
                return false;
            }
        }
        //  2. Handle acl resource
        if (!empty($column['resource']) && !$viewModel->acl()->isAllowed(null, $column['resource'], $column['privilege'])) {
            return false;
        }
        //  3. Handle acl resource
        if (!empty($column['role'])) {
            $allowed = (array) $column['role'];
            $role = $viewModel->identity()->getUserRole()->getName();
            if (!in_array($role, $allowed,true)) {
                return false;
            }
        }
        return true;
    }
}
