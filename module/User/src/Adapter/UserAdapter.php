<?php
/**
 * Class UserAdapter
 * @package User\Adapter
 *
 * since: 30.07.2020
 * author: alexej@kisselev.de
 */

namespace User\Adapter;


use Application\Form\StatusEdit;
use Application\ServiceManager\Interfaces\Constant;
use Application\View\Manager\EmptyAdapter;
use User\Controller\UserController;
use User\Entity\User;
use User\Form\UserCreate;
use User\Form\UserEdit;
use User\Repository\UserDao;

class UserAdapter extends EmptyAdapter
{
    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return User::class;
    }

    /*public function getLayoutSpecifications(): array
    {
        $layout = [
            //'title' => 'User Management'
        ];
        switch ($this->getAction()) {
            case 'list':
                $layout['toolbar'] = 'toolbar/default-list';
                break;
            case 'edit':
            case 'create':
                $layout['toolbar'] = 'toolbar/default-edit';
                break;
        }
        return $layout;
    }*/

    public function getFormName(): string
    {
        switch ($this->getActionName()) {
            case 'edit':
                return UserEdit::class;
            case 'create':
                return UserCreate::class;
            case 'status':
                return StatusEdit::class;
        }
        return '';
    }

    public function getContentSpecifications(): array
    {
        $content = [
            //'title' => 'User Management'
        ];
        switch ($this->getActionName()) {
            case 'edit':
                $content['form'] = UserEdit::class;
                break;
            case 'create':
                $content['form'] = UserCreate::class;
                break;
        }
        return $content;
    }

    /**
     * @param string $columnName
     * @return bool
     */
    public function isColumnVisible(string $columnName): bool
    {
        switch (strtolower($columnName)) {
            case 'createid':
            case 'created':
            case 'changeid':
            case 'changed':
            case 'password':
            case 'passwordresettoken':
            case 'passwordresettokencreationdate':
            case 'attributevalues':
            case 'configs':
                return false;
        }
        return true;
    }

    public function getActionsSpecifications(): array
    {
        $actions = [];

            $actions[] = [
                'icon'  => 'la la-edit',
                'route' => [
                    'name'   => 'default/admin',
                    'params' => [
                        'controller' => 'user',
                        'action'     => 'edit',
                    ],
                ],
                'attr'  => [
                    'title' => 'Edit user profile',
                ]
            ];


            $actions[] = [
                'icon'  => 'la la-check-double',
                'route' => [
                    'name'   => 'default/admin',
                    'params' => [
                        'controller' => 'user',
                        'action'     => 'status',
                    ],
                ],
                'attr'  => [
                    'title'       => 'Change user status',
                    'data-toggle' => 'modal',
                    'data-target' => "#ajax-modal"
                ]
            ];

        return $actions;
    }
}