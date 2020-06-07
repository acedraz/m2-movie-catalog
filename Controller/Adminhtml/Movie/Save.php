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
use Aislan\MovieCatalog\Api\Data\MovieEntityInterfaceFactory;
use Aislan\MovieCatalog\Api\MovieApiRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;

class Save extends Action
{

    /**
     * @var MovieEntityRepositoryInterfaceFactory
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
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * [__construct description].
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param MovieEntityRepositoryInterface $movieEntityRepository
     * @param MovieEntityInterfaceFactory $movieEntityFactory
     * @param MovieApiRepositoryInterface $movieApiRepository
     */
    public function __construct(
        Context $context,
        MovieEntityRepositoryInterface $movieEntityRepository,
        MovieEntityInterfaceFactory $movieEntityFactory,
        MovieApiRepositoryInterface $movieApiRepository,
        DataPersistorInterface $dataPersistor
    ){
        $this->movieEntityRepository = $movieEntityRepository;
        $this->movieEntityFactory = $movieEntityFactory;
        parent::__construct($context);
        $this->movieApiRepository = $movieApiRepository;
        $this->dataPersistor = $dataPersistor;
    }

    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $movieEntitys = $this->movieEntityRepository->getMovieEntityByApiId($data['api_id']);
            foreach ($movieEntitys->getItems() as $item) {
                if (is_null($item->getEntityId())) {
                    $this->messageManager->addErrorMessage(__('This movie no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            $movieEntity = $this->movieEntityFactory->create();
            $movieEntity->setData($data);
            try {
                $this->movieEntityRepository->save($movieEntity);
                $this->messageManager->addSuccessMessage(__('You saved the Movie.'));
                $this->dataPersistor->clear('aislan_moviecatalog_movie_entity');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $movieEntity->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Movie.'));
            }
            $this->dataPersistor->set('aislan_moviecatalog_movie_entity', $data);
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
