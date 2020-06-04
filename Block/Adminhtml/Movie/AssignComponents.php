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

namespace Aislan\MovieCatalog\Block\Adminhtml\Movie;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Json\EncoderInterface;
use Aislan\MovieCatalog\Block\Adminhtml\Movie\Tab\Component;

class AssignComponents extends Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'Avanti_MosaicManager::mosaic/edit/assign_components.phtml';

    /**
     * @var Component
     */
    protected $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * AssignComponents constructor.
     *
     * @param Context          $context
     * @param Registry         $registry
     * @param EncoderInterface $jsonEncoder
     * @param array            $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block.
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(Component::class, 'avanti_mosaicmanager.mosaic.component.grid');
        }

        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block.
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getComponentsJson()
    {
        $components = $this->getMosaic()->getComponentsPosition();
        if (!empty($components)) {
            return $this->jsonEncoder->encode($components);
        }

        return '{}';
    }

    /**
     * Retrieve current mosaic instance.
     *
     * @return array|null
     */
    public function getMosaic()
    {
        return $this->registry->registry('aislan_moviecatalog_movie');
    }
}
