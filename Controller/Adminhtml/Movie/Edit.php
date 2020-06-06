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
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 *  Edit
 */
class Edit extends Movie
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var MosaicFactory
     */
    private $movieEntityRepository;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param MovieEntityRepositoryInterface $movieEntityRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PageFactory $resultPageFactory,
        MovieEntityRepositoryInterface $movieEntityRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->movieEntityRepository = $movieEntityRepository;
        parent::__construct($context, $registry);
    }

    /**
     * Edit action.
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $movieEntityId = (int) $this->getRequest()->getParam('entity_id');
        if ($movieEntityId) {
            $moviesEntitys = $this->movieEntityRepository->getMovieEntityById($movieEntityId);
            foreach ($moviesEntitys->getItems() as $item) {
                $moviesEntity = $item;
            }
            if (!$moviesEntity->getEntityId()) {
                $this->messageManager->addErrorMessage(__('This movie no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->registry->register('aislan_moviecatalog_movie_entity', $moviesEntity);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $movieEntityId ? __('Edit Movie') : __('New Movie'),
            $movieEntityId ? __('Edit Movie') : __('New Movie')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Mosaics'));
        $resultPage->getConfig()->getTitle()->prepend(
            $moviesEntity->getEntityId() ? __('Edit Movie %1', $moviesEntity->getOriginalTitle()) : __('New movie')
        );

        return $resultPage;
    }
}
