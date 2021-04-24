<?php

namespace Product\Adapter;

use Application\Form\StatusEdit;
use Application\ServiceManager\Interfaces\Constant;
use Application\View\Manager\EmptyAdapter;
use Exception;
use Product\Controller\ProductController;
use Product\Entity\Product;
use Product\Form\ProductEdit;
use Product\Repository\ProductDao;

class ProductAdapter extends EmptyAdapter
{
    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return Product::class;
    }

    public function getFormName(): string
    {
        if ($this->getActionName() === 'status') {
            return StatusEdit::class;
        }
        return ProductEdit::class;
    }

    /**
     * @param string $columnName
     * @return bool
     */
    public function isColumnVisible(string $columnName): bool
    {
        switch (strtolower($columnName)) {
            case 'createid':
                return true;
            case 'created':
            case 'changeid':
            case 'changed':
                return false;
        }
        $yes = parent::isColumnVisible($columnName);
        if ($yes) {
            return $this->getAcl()->isAllowedColumn(null, ProductController::class, $columnName);
        }
        return false;
    }

    public function getColumnsSpecifications(): array
    {


        $specificationsA = [
            'statistics'      => [
                'name'       => 'statistics',
                'label'      => 'statistics',
                'sortOrder'  => 100,
                'unsortable' => true,
                'hidden'     => false,
                'type'       => 'string'
            ],
            'statisticsToday' => [
                'name'       => 'statisticsToday',
                'label'      => 'Today',
                'sortOrder'  => 100,
                'unsortable' => true,
                'hidden'     => false,
                'type'       => 'string'
            ],
        ];

        /** @var ProductDao $dao */
        $dao = $this->getEntityManager()->getRepository(Product::class);
        try {
            foreach ($dao->getNamedQueryResult(Constant::NAMED_QUERY_ATTRIBUTE_COLUMNS) as $col) {
                $specificationsA[$col['name']] = $col;
            }
        } catch (Exception $e) {
        }
        $specificationsA['creative']['type'] = 'string';

        $specifications = [];
        $res = array_replace_recursive($specificationsA, $specifications);
        $columns = [];
        foreach ($res as $columnName => $specification) {
            if ($this->isColumnVisible($columnName)) {
                $columns[$columnName] = $specification;
            }
        }
        return $columns;
    }

    public function getActionsSpecifications(): array
    {
        $actions = [];
        if ($this->getAcl()->isAllowed(null, ProductController::class, 'edit')) {
            $actions[] = [
                'icon'  => 'la la-edit',
                'route' => [
                    'name'   => 'default/product',
                    'params' => [
                        'controller' => 'campaign',
                        'action'     => 'edit',
                    ],
                ],
                'attr'  => [
                    'title' => 'Edit campaign',
                ]
            ];
        }
        if ($this->getServiceManager()->has('productStatistics')) {
            $actions[] = [
                'icon'  => 'la la-info',
                'route' => [
                    'name'   => 'default/product',
                    'params' => [
                        'controller' => 'statistics',
                        'action'     => 'list',
                    ],
                ],
                'attr'  => [
                    'title' => 'Edit campaign',
                ]
            ];
        }


        if ($this->getAcl()->isAllowed(null, ProductController::class, 'status')) {
            $actions[] = [
                'icon'  => 'la la-check-double',
                'route' => [
                    'name'   => 'default/product',
                    'params' => [
                        'controller' => 'campaign',
                        'action'     => 'status',
                    ],
                ],
                'attr'  => [
                    'title'       => 'Change campaign status',
                    'data-toggle' => 'modal',
                    'data-target' => "#ajax-modal"
                ]
            ];
            $actions[] = [
                'icon'  => 'la la-remove',
                'route' => [
                    'name'   => 'default/product',
                    'params' => [
                        'controller' => 'campaign',
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
        }
        return $actions;
    }

    public function getLayoutSpecifications(): array
    {
        $layout = parent::getLayoutSpecifications();
        if ($this->getActionName() === 'create') {
            // hide button "Save and back to list" on toolbar
            $layout['hideBack'] = true;
        }
        return $layout;
    }

    public function getContentSpecifications(): array
    {
        $content = parent::getContentSpecifications();
        if ($this->getActionName() === 'create') {
            // hide button "Save and back to list" for a form
            $content['hideBack'] = true;
        }
        return $content;
    }
}
