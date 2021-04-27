<?php
/**
 * Class UserAdapter
 * @package Category\Adapter
 *
 * since: 30.07.2020
 * author: alexej@kisselev.de
 */

namespace Category\Adapter;

use Application\View\Manager\EmptyAdapter;
use Category\Entity\Category;
use Category\Form\CategoryCreate;
use Category\Form\CategoryEdit;


class CategoryAdapter extends EmptyAdapter
{
    public function getEntityName(): string
    {
        return Category::class;
    }

    public function getFormName(): string
    {
        if ($this->getActionName() === 'create') {
                return CategoryCreate::class;
        }
        return CategoryEdit::class;
    }

    public function getLayoutSpecifications(): array
    {

        $layout = parent::getLayoutSpecifications();
        switch ($this->getActionName()) {
            case 'reorder':
                $layout['toolbar'] = 'toolbar/default-edit';
                break;
        }
        return $layout;
    }

    public function isColumnVisible(string $columnName): bool
    {
        $yes = parent::isColumnVisible($columnName);
        if (!$yes) {
            return false;
        }
        switch ($columnName) {
            case 'lft':
            case 'lft':
            case 'children':
                return false;
        }
        return true;
    }

    public function getContentSpecifications(): array
    {
        $content = parent::getContentSpecifications();
        if ($this->getActionName() === 'delete') {
            $content['title'] = 'Delete Confirm';
        }
        return $content;
    }

    public function getActionsSpecifications(): array
    {
        $actions = parent::getActionsSpecifications();

        $actions[] = [
            'icon'  => 'la la-remove',
            'route' => [
                'name'   => 'default/admin',
                'params' => [
                    'controller' => $this->getControllerAlias(),
                    'action'     => 'delete',
                ],
            ],
            'attr'  => [
                'title'       => 'Delete',
                'class'       => 'btn btn-sm btn-hover-light-danger btn-clean btn-icon',
                'data-toggle' => 'modal',
                'data-target' => "#ajax-modal"
            ]
        ];

        return $actions;
    }
}
