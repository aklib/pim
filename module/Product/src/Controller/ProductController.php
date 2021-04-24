<?php /** @noinspection ContractViolationInspection */

namespace Product\Controller;

use Application\Controller\AbstractModuleController;
use Application\Custom\Column;
use Application\Custom\ResultRow;
use Application\Form\EntityEdit;
use Application\Form\StatusEdit;
use Application\View\Model\Tab\TabItem;
use Attribute\Entity\AttributeTab;
use Attribute\Entity\AttributeValueCreative;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\ORMException;
use Exception;
use FlorianWolters\Component\Core\StringUtils;
use Laminas\Form\Element\Select;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\Http\Response;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use Product\Entity\Product;
use Product\Entity\ProductAttributeValue;
use Product\Entity\ProductCreativeFile;
use Product\ModuleOptions;
use Report\Entity\Bid;
use Report\Repository\BidDao;
use RuntimeException;

/**
 * This controller is responsible for product management (adding, editing, viewing products).
 */
class ProductController extends AbstractModuleController
{

    public function listAction(ViewModel $viewModel = null, array $options = []): ViewModel
    {

        if ($viewModel === null) {
            $viewModel = parent::listAction($viewModel, $options);
        }
        // ========== INIT STATISTICS ROWS ==========

        /** @var ViewModel $dataModel */
        $dataModel = $viewModel->getChildrenByCaptureTo('data')[0];
        $data = $dataModel->getVariable('data');
        if (is_iterable($data)) {
            $ids = [];
            /** @var Product $campaign */
            foreach ($data as $campaign) {
                $ids[] = $campaign->getId();
            }
            /** @var BidDao $dao */
            $dao = $this->getEntityManager()->getRepository(Bid::class);
            $result = $dao->getStatistics($ids);
//            dump($result);die;
            /** @var Product $campaign */
            foreach ($data as $campaign) {
                if (isset($result[$campaign->getId()])) {
                    $resultRow = $this->createStatistics($result[$campaign->getId()]);
                    $resultRow->setOk((bool)$result['success']);
                    $campaign->setStatistics($resultRow);
                }
            }

            $result = $dao->getStatisticsToday($ids);
            /** @var Product $campaign */
            foreach ($data as $campaign) {
                if (isset($result[$campaign->getId()])) {
                    $resultRow = $this->createStatistics($result[$campaign->getId()]);
                    $resultRow->setOk((bool)$result['success']);
                    $campaign->setStatisticsToday($resultRow);
                }
            }
        }
        return $viewModel;
    }

    private function createStatistics(array $result): ResultRow
    {
        $resultRow = new ResultRow();
        $val = $result['bidCount']['value'] ?? 0;
        $column = [
            'name'       => 'bidCount',
            'label'      => 'Bids',
            'sort_order' => 1,
            'value'      => number_format($val, 0, ',', '.')
        ];
        $resultRow->addColumn(new Column($column));
        $val = $result['bidWon']['value'] ?? 0;
        $column = [
            'name'       => 'bidWon',
            'label'      => 'Bid Won',
            'sort_order' => 2,
            'value'      => number_format($val, 0, ',', '.')
        ];
        $resultRow->addColumn(new Column($column));

        $val = $result['bidClicked']['value'] ?? 0;
        $column = [
            'name'       => 'bidClicked',
            'label'      => 'Bid Clicked',
            'sort_order' => 2,
            'value'      => number_format($val, 0, ',', '.')
        ];
        $resultRow->addColumn(new Column($column));
        if (array_key_exists('cardinalityIp', $result)) {
            $val = $result['cardinalityIp']['buckets'][0]['key'] ?? 0;
            $column = [
                'name'       => 'cardinalityIp',
                'label'      => 'IP',
                'sort_order' => 2,
                'value'      => number_format($val, 0, ',', '.')
            ];
            $resultRow->addColumn(new Column($column));
        }

        $val = round($result['costs']['value'] ?? 0, 4);
        $column = [
            'name'       => 'costs',
            'label'      => 'Sum',
            'sort_order' => 3,
            'value'      => number_format($val, 4, ',', '.')
        ];
        $resultRow->addColumn(new Column($column));
        $resultRow->sort();
        return $resultRow;
    }

    /**
     * @param object|null $entity
     * @param Form|null $form
     * @return Response|ViewModel|null
     * @throws Exception
     */
    public function editAction(?object $entity = null, ?Form $form = null)
    {
        return parent::editAction($entity, $form);
    }


    protected function postCreateTab(TabItem $tabItem, AttributeTab $attributeTab = null): void
    {
        parent::postCreateTab($tabItem, $attributeTab);
        if ($attributeTab !== null && $attributeTab->getId() === 5) {
            // todo handle attribute tabs
            //  countries
            /** @var ViewModel $content */
            $content = $tabItem->getContent();
            $fieldset = $content->getVariable('form');
            if (!$fieldset instanceof Fieldset || !$fieldset->has('country') || !($fieldset->get('country') instanceof Select)) {
                return;
            }
            /** @var Select $select */
            $select = $fieldset->get('country');
            $valueOptions = $select->getValueOptions();

            $content->setTemplate('/form/partial/checkboxes.phtml');
            $content->setVariables([
                'countColumns' => 6,
                'items'        => $valueOptions,
                'fieldName'    => $select->getName(),
                'values'       => $select->getValue()
            ]);
        }
    }

    /**
     * Handle file uploads
     * @param array $data
     * @param object|null $entity
     * @return array
     */
    protected function postValidateFormData(array $data, object $entity = null): array
    {
        if (empty($this->getRequest()->getFiles())) {
            return parent::postValidateFormData($data);
        }
        $filesByElement = $this->getRequest()->getFiles()->toArray();
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $this->getServiceManager()->get(ModuleOptions::class);
        $targetDirectory = $moduleOptions->getUploadPath();
        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0775, true) && !is_dir($targetDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $targetDirectory));
        }

        foreach ($filesByElement as $name => $files) {
            if (isset($files['creativeFiles']) && is_array($files['creativeFiles'])) {
                foreach ($files['creativeFiles'] as $id => $file) {
                    if (empty($file['name'])) {
                        // no images uploaded
                        continue;
                    }
                    $fileName = StringUtils::substringBeforeLast($file['name'], '.');
                    $fileHash = md5_file($file['tmp_name']);
                    $filePath = $targetDirectory . '/' . str_replace($fileName, $fileHash, $file['name']);
                    $result = move_uploaded_file($file['tmp_name'], $filePath);
                    if ($result) {
                        $size = getimagesize($filePath);
                        $fileData = [
                            'width'  => $size[0],
                            'height' => $size[1],
                            'uri'    => str_replace(APPLICATION_PATH . '/public', '', $filePath)
                        ];
                        if (is_numeric($id)) {
                            $fileData['id'] = (int)$id;
                            $data[$name]['creativeFiles'][(int)$id] = $fileData;
                        } else {
                            $data[$name]['creativeFiles'][] = $fileData;
                        }
                    } else {
                        $this->addMessage(sprintf("The image '%s' is not saved", $file['name']), FlashMessenger::NAMESPACE_ERROR);
                    }
                }
            }
        }
        if ($entity instanceof Product) {
            // do not remove a file if only one other has been uploaded/changed

            /** @var Collection $attributeValues */
            $attributeValues = $entity->getAttributeValues();
            /** @var ProductAttributeValue $attributeValue */
            foreach ($attributeValues as $attributeValue) {
                if ($attributeValue->getAttribute()->getType()->getType() !== 'creative') {
                    continue;
                }
                $attributeValueCreatives = $attributeValue->getValueCreatives();
                if (!$attributeValueCreatives->isEmpty()) {
                    /** @var AttributeValueCreative $attributeValueCreative */
                    $attributeValueCreative = $attributeValueCreatives->first();
                    /** @var ProductCreativeFile $creativeFile */
                    foreach ($attributeValueCreative->getVal()->getCreativeFiles() as $creativeFile) {
                        $fullPath = APPLICATION_PATH . '/public' . $creativeFile->getUri();
                        if (file_exists($fullPath)) {
                            if (!isset($data['creative']['creativeFiles'][$creativeFile->getId()])) {
                                $data['creative']['creativeFiles'][$creativeFile->getId()] =
                                    [
                                        'id'     => $creativeFile->getId(),
                                        'width'  => $creativeFile->getWidth(),
                                        'height' => $creativeFile->getHeight(),
                                        'uri'    => $creativeFile->getUri()
                                    ];
                            }
                        } else {
                            try {
                                $this->getEntityManager()->remove($creativeFile);
                            } catch (ORMException $e) {
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * @return Response|ViewModel|null
     * @throws Exception
     * @noinspection PhpUnused
     * @noinspection NullPointerExceptionInspection
     */
    public function createAction()
    {
        $product = new Product();

        if ($this->identity()->getUserRole()->isRestricted()) {
            if ($this->identity()->getAdvertisers()->isEmpty()) {
                $this->addMessage("please contact your account manager", FlashMessenger::NAMESPACE_ERROR);
                $url = $this->getURL('list', $this->getControllerName());
                return $this->redirect()->toUrl($url);
            }
            $product->setAdvertiser($this->identity()->getAdvertisers()->first());
        }

        /** @var EntityEdit $createForm */
        $createForm = $this->getServiceManager()->get(EntityEdit::class);
        $createForm->createForm($product);
        return $this->editAction($product, $createForm);
    }

    /**
     * @param object|null $entity
     * @param Form|null $form
     * @return ViewModel
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function statusAction(?object $entity = null, ?Form $form = null): ViewModel
    {
        $viewModel = parent::editAction($entity, $form);
        if ($viewModel instanceof ViewModel) {
            /** @var StatusEdit $form */
            $form = $viewModel->getVariable('form');
            $form->setAttribute('action', $this->urlx(['action' => 'status']));
        }
        return $viewModel;
    }

    /**
     * Deletes an attribute
     * @param object|null $entity
     * @param Form|null $form
     * @param array|null $options
     * @return ViewModel
     */
    public function deleteAction(?object $entity = null, ?Form $form = null, ?array $options = []): ViewModel
    {
        return parent::deleteAction($entity, $form, $options);
    }
}
