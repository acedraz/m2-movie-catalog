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

namespace Aislan\MovieCatalog\Block\Adminhtml\Movie\Tab;

use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\App\ObjectManager;
use Aislan\MovieCatalog\Model\MovieEntityFactory;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Backend\Block\Template\Context;
use Aislan\MovieCatalog\Block\Adminhtml\Mosaic\Tab\Render\Image;

class Component extends Extended
{
    /**
     * Core registry.
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var ComponentFactory
     */
    protected $componentFactory;

    /**
     * @var Yesno
     */
    private $yesno;

    /**
     * @param Context       $context
     * @param BackendHelper $backendHelper
     * @param ComponentFactory $componentFactory
     * @param Registry      $coreRegistry
     * @param array         $data
     * @param Yesno|null    $yesno
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        ComponentFactory $componentFactory,
        Registry $coreRegistry,
        array $data = [],
        Yesno $yesno = null
    ) {
        $this->componentFactory = $componentFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->yesno = $yesno ?: ObjectManager::getInstance()->get(Yesno::class);
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('avanti_mosaicmanager_mosaic_components');
        $this->setDefaultSort('component_id');
        $this->setUseAjax(true);
    }

    /**
     * @return array|null
     */
    public function getMosaic()
    {
        return $this->_coreRegistry->registry('aislan_moviecatalog_movie');
    }

    /**
     * @param Column $column
     *
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in mosaic flag
        if ($column->getId() == 'in_mosaic') {
            $componentIds = $this->_getSelectedComponents();
            if (empty($componentIds)) {
                $componentIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.component_id', ['in' => $componentIds]);
            } elseif (!empty($componentIds)) {
                $this->getCollection()->addFieldToFilter('main_table.component_id', ['nin' => $componentIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        if ($this->getMosaic()->getMosaicId()) {
            $this->setDefaultFilter(['in_mosaic' => 1]);
        }

        $collection = $this->componentFactory->create()->getCollection();
        $collection->getSelect()->joinLeft(
            ['av_mc' => $collection->getTable('avanti_mosaicmanager_mosaic_component')],
            'main_table.component_id = av_mc.component_id AND av_mc.mosaic_id='.(int) $this->getRequest()->getParam('mosaic_id', 0),
            ['position']
        );

        $this->setCollection($collection);

        if ($this->getMosaic()->getComponentsReadonly()) {
            $componentIds = $this->_getSelectedComponents();
            if (empty($componentIds)) {
                $componentIds = 0;
            }
            $this->getCollection()->addFieldToFilter('component_id', ['in' => $componentIds]);
        }

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        if (!$this->getMosaic()->getComponentsReadonly()) {
            $this->addColumn(
                'in_mosaic',
                array(
                    'type' => 'checkbox',
                    'name' => 'in_mosaic',
                    'values' => $this->_getSelectedComponents(),
                    'index' => 'component_id',
                    'header_css_class' => 'col-select col-massaction',
                    'column_css_class' => 'col-select col-massaction',
                )
            );
        }
        $this->addColumn(
            'component_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'component_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);

        $this->addColumn(
            'image',
            [
                'header' => __('Image'),
                'index' => 'image',
                'renderer' => Image::class,
            ]
        );

        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'type' => 'number',
                'index' => 'position',
                'editable' => !$this->getMosaic()->getComponentsReadonly(),
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('avanti_mosaicmanager/mosaic/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedComponents()
    {
        $components = $this->getRequest()->getPost('selected_components');
        if ($components === null) {
            $components = $this->getMosaic()->getComponentsPosition();

            return array_keys($components);
        }

        return $components;
    }
}
