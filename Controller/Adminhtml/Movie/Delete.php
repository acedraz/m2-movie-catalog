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

use Aislan\MovieCatalog\Api\MovieEntityRepositoryInterface;
use Aislan\MovieCatalog\Controller\Adminhtml\Movie;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

/**
 *  Edit delete
 */
class Delete extends Movie implements HttpGetActionInterface
{

    /**
     * @var MovieEntityRepositoryInterface
     */
    private $movieEntityRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        MovieEntityRepositoryInterface $movieEntityRepository
    ) {
        parent::__construct(
            $context,
            $registry
        );
        $this->movieEntityRepository = $movieEntityRepository;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $error = 0;
        $movieEntityId = (int) $this->getRequest()->getParam('entity_id');
        try {
            $moviesEntity = $this->movieEntityRepository->getMovieEntityById($movieEntityId);
            foreach ($moviesEntity->getItems() as $item) {
                $this->movieEntityRepository->delete($item);
            }
        } catch (LocalizedException $exception) {
                $this->logger->error($exception->getLogMessage());
                $error = 1;
            }
        if ($error) {
            $this->messageManager->addErrorMessage(
                __(
                    'A total of %1 record(s) haven\'t been deleted. Please see server logs for more details.',
                    $error
                )
            );
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
        }
        $this->messageManager->addSuccessMessage(
            __('A total of 1 record(s) have been deleted.')
        );
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}
