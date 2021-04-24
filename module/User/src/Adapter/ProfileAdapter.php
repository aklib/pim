<?php
/**
 * Class UserAdapter
 * @package User\Adapter
 *
 * since: 30.07.2020
 * author: alexej@kisselev.de
 */

namespace User\Adapter;

use User\Controller\ProfileController;
use User\Form\UserEdit;

class ProfileAdapter extends UserAdapter
{
    /**
     * @param string $columnName
     * @return bool
     */
    public function isColumnVisible(string $columnName): bool
    {
        switch (strtolower($columnName)) {
            case 'password':
            case 'passwordresettoken':
            case 'passwordresettokencreationdate':
                return false;
        }
        return $this->getAcl()->isAllowedColumn(null, ProfileController::class, $columnName);
    }

    public function getLayoutSpecifications(): array
    {
        $layout = [
            //'title' => 'User Management'
        ];
        switch ($this->getActionName()) {
            case 'edit':
            case 'password':
                $layout['toolbar'] = 'toolbar/default-edit';
                break;
        }
        return $layout;
    }

    public function getFormName(): string
    {
        switch ($this->getActionName()) {
            case 'edit':
                return UserEdit::class;
        }
        return '';
    }

    public function getContentSpecifications(): array
    {
        $content = [
            'title' => 'Profile Management'
        ];
        switch ($this->getActionName()) {
            case 'password':
                $content['title'] = 'Change Password';
                break;
            case 'email':
                $content['title'] = 'Change Email';
                break;
        }
        return $content;
    }
}