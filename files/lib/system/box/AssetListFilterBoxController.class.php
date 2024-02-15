<?php

namespace assets\system\box;

use assets\data\category\AssetCategoryNodeTree;
use assets\data\location\AssetLocationNodeTree;
use assets\data\option\AssetOption;
use assets\page\AssetListPage;
use wcf\data\category\AbstractDecoratedCategory;
use wcf\system\box\AbstractBoxController;
use wcf\system\event\EventHandler;
use wcf\system\request\LinkHandler;
use wcf\system\request\RequestHandler;
use wcf\system\WCF;

class AssetListFilterBoxController extends AbstractBoxController
{
    /**
     * @inheritDoc
     */
    protected static $supportedPositions = [
        'contentTop',
        'contentBottom'
    ];

    /**
     * @inheritDoc
     */
    protected function loadContent()
    {
        $activeRequest = RequestHandler::getInstance()->getActiveRequest();

        // read categories
        $activeCategory = $this->getActiveCategory($activeRequest);
        $categoryTree = new AssetCategoryNodeTree(
            'de.xxschrandxx.assets.category',
            ($activeCategory === null) ? 0 : $activeCategory->getObjectID(),
            false,
            $this->getForbittenCategoryIDs($activeRequest)
        );
        $categoryList = $categoryTree->getIterator();

        // read locations
        $activeLocation = $this->getActiveLocation($activeRequest);
        $locationTree = new AssetLocationNodeTree(
            'de.xxschrandxx.assets.location',
            ($activeLocation === null) ? 0 : $activeLocation->getObjectID(),
            false,
            $this->getForbittenLocationIDs($activeRequest)
        );
        $locationList = $locationTree->getIterator();

        $lang = WCF::getLanguage();
        $parameters = [
            'categoryCategoryList' => $categoryList,
            'categoryActiveCategory' => $activeCategory,
            'categoryResetFilterLink' => $this->getCategoryResetFilterLink($activeRequest),
            'locationCategoryList' => $locationList,
            'locationActiveCategory' => $activeLocation,
            'locationResetFilterLink' => $this->getLocationResetFilterLink($activeRequest),
            'showChildCategories' => true,
            'validSortFields' => $this->getValidSortFields($lang),
            'sortField' => $this->getActiveSortField($activeRequest),
            'validSortOrders' => [
                'ASC' => $lang->get('wcf.global.sortOrder.ascending'),
                'DESC' => $lang->get('wcf.global.sortOrder.descending')
            ],
            'sortOrder' => $this->getActiveSortOrder($activeRequest),
            'trash' => $this->getTrashValue($activeRequest),
            'canSeeTrashed' => $this->canSeeTrashed($activeRequest),
            'trashOptions' => [
                0 => $lang->get('wcf.box.de.xxschrandxx.assets.assetListFilter.trash.both'),
                1 => $lang->get('wcf.box.de.xxschrandxx.assets.assetListFilter.trash.yes'),
                2 => $lang->get('wcf.box.de.xxschrandxx.assets.assetListFilter.trash.no')
            ],
            'options' => $this->getOptions($activeRequest),
            'optionsResetFilterLink' => $this->getOptionsResetFilterLink($activeRequest),
            'items' => $this->getItemsPerPage($activeRequest)
        ];
        EventHandler::getInstance()->fireAction($this, 'templateParameters', $parameters);
        $this->content = WCF::getTPL()->fetch(
            'boxAssetListFilter',
            'assets',
            $parameters,
            true
        );
    }

    protected function getActiveCategory($activeRequest): ?AbstractDecoratedCategory
    {
        $activeCategory = null;
        if ($activeRequest !== null) {
            if ($activeRequest->getRequestObject() instanceof AssetListPage) {
                if ($activeRequest->getRequestObject()->category !== null) {
                    $activeCategory = $activeRequest->getRequestObject()->category;
                }
            }
        }

        return $activeCategory;
    }

    protected function getForbittenCategoryIDs($activeRequest): array
    {
        $forbiddenCategoryIDs = [];
        if ($activeRequest !== null) {
            if ($activeRequest->getRequestObject() instanceof AssetListPage) {
                if ($activeRequest->getRequestObject()->forbiddenCategoryIDs !== null) {
                    $forbiddenCategoryIDs = $activeRequest->getRequestObject()->forbiddenCategoryIDs;
                }
            }
        }
        return $forbiddenCategoryIDs;
    }

    protected function getCategoryResetFilterLink($activeRequest): string
    {
        $parameters = [];
        if ($activeRequest !== null) {
            if ($activeRequest->getRequestObject() instanceof AssetListPage) {
                if ($activeRequest->getRequestObject()->canonicalURLParameters !== null) {
                    $parameters = $activeRequest->getRequestObject()->canonicalURLParameters;
                }
            }
        }
        $parameters['categoryID'] = 0;
        return LinkHandler::getInstance()->getControllerLink(AssetListPage::class, $parameters);
    }

    protected function getActiveLocation($activeRequest): ?AbstractDecoratedCategory
    {
        $activeLocation = null;
        if ($activeRequest !== null) {
            if ($activeRequest->getRequestObject() instanceof AssetListPage) {
                if ($activeRequest->getRequestObject()->location !== null) {
                    $activeLocation = $activeRequest->getRequestObject()->location;
                }
            }
        }

        return $activeLocation;
    }

    protected function getForbittenLocationIDs($activeRequest): array
    {
        $forbiddenLocationIDs = [];
        if ($activeRequest !== null) {
            if ($activeRequest->getRequestObject() instanceof AssetListPage) {
                if ($activeRequest->getRequestObject()->forbiddenLocationIDs !== null) {
                    $forbiddenLocationIDs = $activeRequest->getRequestObject()->forbiddenLocationIDs;
                }
            }
        }
        return $forbiddenLocationIDs;
    }

    protected function getLocationResetFilterLink($activeRequest): string
    {
        $parameters = [];
        if ($activeRequest !== null) {
            if ($activeRequest->getRequestObject() instanceof AssetListPage) {
                if ($activeRequest->getRequestObject()->canonicalURLParameters !== null) {
                    $parameters = $activeRequest->getRequestObject()->canonicalURLParameters;
                }
            }
        }
        $parameters['locationID'] = 0;
        return LinkHandler::getInstance()->getControllerLink(AssetListPage::class, $parameters);
    }

    protected function getValidSortFields($lang): array {
        $validSortFields = [
            ASSETS_LEGACYID_ENABLED ? 'legacyID' : 'assetUD' => $lang->get('wcf.global.objectID'),
            'title' => $lang->get('wcf.global.title'),
            'amount' => $lang->get('assets.page.assetList.amount'),
            'nextAudit' => $lang->get('assets.page.assetList.nextAudit'),
            'lastAudit' => $lang->get('assets.page.assetList.lastAudit'),
            'lastModification' => $lang->get('assets.page.assetList.lastModification'),
            'time' => $lang->get('assets.page.assetList.time'),
        ];

        EventHandler::getInstance()->fireAction($this, 'getValidSortFields', $validSortFields);

        return $validSortFields;
    }

    protected function getActiveSortField($activeRequest): string {
        $sortField = ASSETS_LEGACYID_ENABLED ? 'legacyID' : 'assetID';
        if ($activeRequest !== null) {
            if ($activeRequest->getRequestObject() instanceof AssetListPage) {
                if ($activeRequest->getRequestObject()->sortField !== null) {
                    $sortField = $activeRequest->getRequestObject()->sortField;
                }
            }
        }
        return $sortField;
    }

    protected function getActiveSortOrder($activeRequest): string {
        $sortOrder = 'ASC';
        if ($activeRequest !== null) {
            if ($activeRequest->getRequestObject() instanceof AssetListPage) {
                if ($activeRequest->getRequestObject()->sortOrder !== null) {
                    $sortOrder = $activeRequest->getRequestObject()->sortOrder;
                }
            }
        }
        return $sortOrder;
    }

    protected function getTrashValue($activeRequest): int {
        $trash = AssetListPage::FILTER_TRASH_BOTH;
        if ($activeRequest !== null) {
            if ($activeRequest->getRequestObject() instanceof AssetListPage) {
                if ($activeRequest->getRequestObject()->filterTrash !== null) {
                    $trash = $activeRequest->getRequestObject()->filterTrash;
                }
            }
        }
        return $trash;
    }

    protected function canSeeTrashed($activeRequest): bool
    {
        $canSeeTrashed = false;
        if ($activeRequest !== null) {
            if ($activeRequest->getRequestObject() instanceof AssetListPage) {
                if ($activeRequest->getRequestObject()->canSeeTrashed !== null) {
                    $canSeeTrashed = $activeRequest->getRequestObject()->canSeeTrashed;
                }
            }
        }
        return $canSeeTrashed;
    }

    protected function getOptions($activeRequest)
    {
        $optionHandler = $activeRequest->getRequestObject()->optionHandler;
        foreach ($optionHandler->options as $optionName => $option) {
            switch ($option->optionType) {
                case 'multiSelect':
                case 'checkboxes':
                    unset($optionHandler->options[$optionName]);
                    break;
                default:
            }
        }
        $filterCustomOptions = $activeRequest->getRequestObject()->filterCustomOptions;
        $values = [];
        foreach ($filterCustomOptions as $optionID => $value) {
            $values['customOption' . $optionID] = $value;
        }
        $optionHandler->setOptionValues($values);
        return $optionHandler->getOptions();
    }

    protected function getOptionsResetFilterLink($activeRequest): string
    {
        $parameters = [];
        if ($activeRequest !== null) {
            if ($activeRequest->getRequestObject() instanceof AssetListPage) {
                if ($activeRequest->getRequestObject()->canonicalURLParameters !== null) {
                    $parameters = $activeRequest->getRequestObject()->canonicalURLParameters;
                }
            }
            $optionHandler = $activeRequest->getRequestObject()->optionHandler;
            /** @var AssetOption $option */
            foreach ($optionHandler->options as $optionName => $option) {
                unset($parameters[$optionName]);
            }
        }
        return LinkHandler::getInstance()->getControllerLink(AssetListPage::class, $parameters);
    }

    protected function getItemsPerPage($activeRequest): int {
        $items = 20;
        if ($activeRequest !== null) {
            if ($activeRequest->getRequestObject() instanceof AssetListPage) {
                if ($activeRequest->getRequestObject()->itemsPerPage !== null) {
                    $items = $activeRequest->getRequestObject()->itemsPerPage;
                }
            }
        }
        return $items;
    }
}
