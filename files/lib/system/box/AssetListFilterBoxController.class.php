<?php

namespace assets\system\box;

use assets\data\category\AssetCategoryNodeTree;
use assets\data\location\AssetLocationNodeTree;
use assets\page\AssetListPage;
use wcf\data\category\AbstractDecoratedCategory;
use wcf\system\box\AbstractBoxController;
use wcf\system\request\LinkHandler;
use wcf\system\request\RequestHandler;
use wcf\system\WCF;

class AssetListFilterBoxController extends AbstractBoxController
{
    /**
     * @inheritDoc
     */
    protected static $supportedPositions = [
        'sidebarLeft',
        'sidebarRight',
        'contentTop',
        'contentBottom',
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
            $this->getForbittenCategoryIDs($activeRequest));
        $categoryList = $categoryTree->getIterator();

        // read locations
        $activeLocation = $this->getActiveLocation($activeRequest);
        $locationTree = new AssetLocationNodeTree(
            'de.xxschrandxx.assets.location',
            ($activeLocation === null) ? 0 : $activeLocation->getObjectID(),
            false,
            $this->getForbittenLocationIDs($activeRequest));
        $locationList = $locationTree->getIterator();

        $this->content = WCF::getTPL()->fetch(
            'boxAssetListFilter',
            'assets',
            [
                'categoryCategoryList' => $categoryList,
                'categoryActiveCategory' => $activeCategory,
                'categoryResetFilterLink' => $this->getCategoryResetFilterLink($activeRequest),
                'locationCategoryList' => $locationList,
                'locationActiveCategory' => $activeLocation,
                'locationResetFilterLink' => $this->getLocationResetFilterLink($activeRequest),
                'showChildCategories' => true,
                'canSeeTrashed' => $this->canSeeTrashed($activeRequest)
            ],
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
}