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

namespace Aislan\MovieCatalog\Controller\Movie;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class View extends Action
{

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $block = $page->getLayout()->getBlock('catalog_movie_view');
        $block->setData('movieEntityId',$params['id']);
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
