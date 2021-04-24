<?php
/**
 * Class UserAdapter
 * @package Category\Adapter
 *
 * since: 30.07.2020
 * author: alexej@kisselev.de
 */

namespace Category\Adapter;

use Application\Form\ConfirmForm;
use Application\View\Manager\EmptyAdapter;
use Category\Entity\Category;
use Category\Form\CategoryEdit;


class CategoryAdapter extends EmptyAdapter
{
    public function getEntityName(): string
    {
        return Category::class;
    }

    public function getFormName(): string
    {
        switch ($this->getActionName()) {
            case 'edit':
            case 'create':
                return CategoryEdit::class;
        }
        return parent::getFormName();
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
        if ($this->getAcl()->isAllowed(null, $this->getControllerName(), 'edit')) {
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
                    'title' => 'Delete',
                    'class'=>'btn btn-sm btn-hover-light-danger btn-clean btn-icon',
                    'data-toggle' => 'modal',
                    'data-target' => "#ajax-modal"
                ]
            ];
        }
        return $actions;
    }
}
