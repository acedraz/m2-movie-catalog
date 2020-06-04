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

namespace Aislan\MovieCatalog\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Backend\App\Action;

abstract class Movie extends Action
{
    const ADMIN_RESOURCE = 'Aislan_MovieCatalog::catalog_movies';

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Context  $context
     * @param Registry $registry
     */
    public function __construct(Context $context, Registry $registry)
    {
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * Init page.
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Aislan'), __('Aislan'))
            ->addBreadcrumb(__('Movie Catalog'), __('Movie Catalog'));

        return $resultPage;
    }
}
