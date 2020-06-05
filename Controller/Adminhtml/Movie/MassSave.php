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

use Aislan\MovieCatalog\Api\MovieApiRepositoryInterface;
use Aislan\MovieCatalog\Api\MovieEntityRepositoryInterface;
use Aislan\MovieCatalog\Controller\Adminhtml\Movie;
use Aislan\MovieCatalog\Model\ResourceModel\MovieApi\CollectionFactory;
use Aislan\MovieCatalog\Model\MovieEntity;
use Aislan\MovieCatalog\Api\Data\MovieEntityInterfaceFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

/**
 * Class massSave
 */
class MassSave extends Movie implements HttpPostActionInterface
{

    /**
     * @var MovieEntityRepositoryInterface
     */
    private $movieEntityRepository;

    /**
     * @var MovieEntityInterfaceFactory
     */
    private $movieEntityFactory;

    /**
     * @var MovieApiRepositoryInterface
     */
    private $movieApiRepository;

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
     * massSave constructor.
     * @param MovieEntityRepositoryInterface $movieEntityRepository
     * @param MovieEntityInterfaceFactory $movieEntityFactory
     * @param MovieApiRepositoryInterface $movieApiRepository
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param LoggerInterface|null $logger
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        MovieEntityRepositoryInterface $movieEntityRepository,
        MovieEntityInterfaceFactory $movieEntityFactory,
        MovieApiRepositoryInterface $movieApiRepository,
        Filter $filter,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger = null,
        Context $context,
        Registry $registry
    ) {
        $this->movieEntityRepository = $movieEntityRepository;
        $this->movieEntityFactory = $movieEntityFactory;
        $this->movieApiRepository = $movieApiRepository;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
        parent::__construct($context,$registry);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $movieSaved = 0;
        $movieSavedError = 0;
        foreach ($collection->getItems() as $movie) {
            try {
                $movieEntity = $this->movieEntityFactory->create();
                foreach ($movieEntity->getEntries() as $entry) {
                    if ($entry != MovieEntity::ID) {
                        $movieEntity->setData($entry,$movie->getData($entry));
                    }
                }
                $this->movieEntityRepository->save($movieEntity);
                $movieSaved++;
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getLogMessage());
                $movieSavedError++;
            }
        }
        if ($movieSaved) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been added.', $movieSaved)
            );
        }
        if ($movieSavedError) {
            $this->messageManager->addErrorMessage(
                __(
                    'A total of %1 record(s) haven\'t been added. Please see server logs for more details.',
                    $movieSavedError
                )
            );
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}
