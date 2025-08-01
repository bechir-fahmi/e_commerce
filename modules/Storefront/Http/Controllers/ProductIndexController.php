<?php

namespace Modules\Storefront\Http\Controllers;

use Illuminate\Support\Collection;
use Modules\Product\RecentlyViewed;
use Modules\Product\Entities\Product;
use Modules\Category\Entities\Category;

class ProductIndexController
{
    private $recentlyViewed;


    public function __construct(RecentlyViewed $recentlyViewed)
    {
        $this->recentlyViewed = $recentlyViewed;
    }


    protected function getProducts($settingPrefix)
    {
        $type = setting("{$settingPrefix}_product_type", 'custom_products');
        $limit = setting("{$settingPrefix}_products_limit");

        if ($type === 'category_products') {
            return $this->categoryProducts($settingPrefix, $limit);
        }

        if ($type === 'recently_viewed_products') {
            return $this->recentlyViewedProducts($limit);
        }

        return Product::forCard()
            ->when($type === 'latest_products', $this->latestProductsCallback($limit))
            ->when($type === 'custom_products', $this->customProductsCallback($settingPrefix))
            ->get()
            ->map
            ->clean();
    }


    private function categoryProducts($settingPrefix, $limit)
    {
        return Category::findOrNew(setting("{$settingPrefix}_category_id"))
            ->products()
            ->latest()
            ->forCard()
            ->take($limit)
            ->get();
    }


    private function recentlyViewedProducts($limit)
    {
        return collect($this->recentlyViewed->products())
            ->reverse()
            ->when(!is_null($limit), function (Collection $products) use ($limit) {
                return $products->take($limit);
            })
            ->values();
    }


    private function latestProductsCallback($limit)
    {
        return function ($query) use ($limit) {
            $query->latest()
                ->when(!is_null($limit), function ($q) use ($limit) {
                    $q->limit($limit);
                });
        };
    }


    private function customProductsCallback($settingPrefix)
    {
        return function ($query) use ($settingPrefix) {
            $productIds = setting("{$settingPrefix}_products", []);

            $query->whereIn('id', $productIds)
                ->when(!empty($productIds), function ($q) use ($productIds) {
                    $productIdsString = collect($productIds)->filter()->implode(',');

                    $q->orderByRaw("FIELD(id, {$productIdsString})");
                });
        };
    }
}
