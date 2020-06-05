<?php
/**
 * Aislan
 *
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to aislan.cedraz@gmail.com.br for more information.
 *
 * @module      Aislan Movie Catalog
 * @category    Aislan
 * @package     Aislan_MovieCatalog
 *
 * @copyright   Copyright (c) 2020 Aislan.
 *
 * @author      Aislan Core Team <aislan.cedraz@gmail.com.br>
 */

declare(strict_types=1);

namespace Aislan\MovieCatalog\Controller\Adminhtml\Movie;

use Aislan\MovieCatalog\Controller\Adminhtml\Movie;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use Aislan\MovieCatalog\Model\ResourceModel\MovieEntity\CollectionFactory;
use Aislan\MovieCatalog\Api\MovieEntityRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Class MassDelete
 */
class MassDelete extends Movie implements HttpPostActionInterface
{
    /**
     * @var Builder
     */
    private $productBuilder;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var MovieEntityRepositoryInterface
     */
    private $movieEntityRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        Builder $productBuilder,
        Filter $filter,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger = null,
        MovieEntityRepositoryInterface $movieEntityRepository
    ) {
        parent::__construct($context, $registry);
        $this->productBuilder = $productBuilder;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
        $this->movieEntityRepository = $movieEntityRepository;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $movieDeleted = 0;
        $movieDeletedError = 0;
        foreach ($collection->getItems() as $movieEntity) {
            try {
                $this->movieEntityRepository->delete($movieEntity);
                $movieDeleted++;
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getLogMessage());
                $movieDeletedError++;
            }
        }
        if ($movieDeleted) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $movieDeleted)
            );
        }
        if ($movieDeletedError) {
            $this->messageManager->addErrorMessage(
                __(
                    'A total of %1 record(s) haven\'t been deleted. Please see server logs for more details.',
                    $movieDeletedError
                )
            );
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}
