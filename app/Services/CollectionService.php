<?php

namespace App\Services;

use App\Models\Collection;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class CollectionService
{
    public function getCollectionProducts(Collection $collection, int $perPage = 12): LengthAwarePaginator
    {
        if ($collection->collection_type === 'manual') {
            return $this->getManualCollectionProducts($collection, $perPage);
        }

        return $this->getAutomaticCollectionProducts($collection, $perPage);
    }

    public function getManualCollectionProducts(Collection $collection, int $perPage = 12): LengthAwarePaginator
    {
        return $collection->products()->with(['reviews' => fn ($q) => $q->where('is_approved', true)])->paginate($perPage);
    }

    public function getAutomaticCollectionProducts(Collection $collection, int $perPage = 12): LengthAwarePaginator
    {
        $conditions = $collection->conditions;

        if ($conditions->isEmpty()) {
            return Product::query()->whereRaw('1 = 0')->paginate($perPage);
        }

        $query = Product::query()->with(['category', 'brand', 'tags', 'reviews' => fn ($q) => $q->where('is_approved', true)]);

        $matchType = $collection->match_type ?? 'all';

        foreach ($conditions as $condition) {
            $method = $matchType === 'any' ? 'orWhere' : 'where';

            $this->applyCondition($query, $condition->condition_field, $condition->condition_operator, $condition->condition_value, $method);
        }

        return $query->paginate($perPage);
    }

    public function getAutomaticCollectionQuery(Collection $collection): Builder
    {
        $conditions = $collection->conditions;

        if ($conditions->isEmpty()) {
            return Product::query()->whereRaw('1 = 0');
        }

        $query = Product::query()->with(['category', 'brand', 'tags']);
        $matchType = $collection->match_type ?? 'all';

        foreach ($conditions as $condition) {
            $method = $matchType === 'any' ? 'orWhere' : 'where';
            $this->applyCondition($query, $condition->condition_field, $condition->condition_operator, $condition->condition_value, $method);
        }

        return $query;
    }

    public function previewAutomaticQuery(array $conditions, string $matchType = 'all', int $perPage = 5): LengthAwarePaginator
    {
        if (empty($conditions)) {
            return Product::query()->whereRaw('1 = 0')->paginate($perPage);
        }

        $query = Product::query()->with(['category', 'brand']);
        $method = $matchType === 'any' ? 'orWhere' : 'where';

        foreach ($conditions as $condition) {
            $this->applyCondition(
                $query,
                $condition['condition_field'] ?? $condition['field'],
                $condition['condition_operator'] ?? $condition['operator'],
                $condition['condition_value'] ?? $condition['value'],
                $method
            );
        }

        return $query->paginate($perPage);
    }

    protected function applyCondition(Builder $query, string $field, string $operator, string $value, string $method = 'where'): void
    {
        $fieldMap = [
            'title' => 'products.name',
            'product_type' => 'products.product_type',
            'sku' => 'products.sku',
            'price' => 'products.price',
            'sale_price' => 'products.sale_price',
            'stock' => 'products.stock',
            'status' => 'products.status',
            'brand' => 'brand_id',
            'category' => 'category_id',
            'tag' => 'tags',
        ];

        if (! isset($fieldMap[$field])) {
            return;
        }

        $dbField = $fieldMap[$field];

        if ($field === 'tag') {
            $this->applyTagCondition($query, $operator, $value, $method);

            return;
        }

        if ($field === 'brand') {
            $this->applyBrandCondition($query, $operator, $value, $method);

            return;
        }

        if ($field === 'category') {
            $this->applyCategoryCondition($query, $operator, $value, $method);

            return;
        }

        $textOperators = ['equals', 'not_equals', 'contains', 'starts_with', 'ends_with'];
        $numericFields = ['price', 'sale_price', 'stock'];

        if (in_array($operator, $textOperators)) {
            $this->applyTextCondition($query, $dbField, $operator, $value, $method);
        } elseif (in_array($field, $numericFields)) {
            $this->applyNumericCondition($query, $dbField, $operator, $value, $method);
        }
    }

    protected function applyTextCondition(Builder $query, string $field, string $operator, string $value, string $method): void
    {
        $query->$method(function (Builder $q) use ($field, $operator, $value) {
            switch ($operator) {
                case 'equals':
                    $q->where($field, '=', $value);
                    break;
                case 'not_equals':
                    $q->where($field, '!=', $value);
                    break;
                case 'contains':
                    $q->where($field, 'like', '%'.$value.'%');
                    break;
                case 'starts_with':
                    $q->where($field, 'like', $value.'%');
                    break;
                case 'ends_with':
                    $q->where($field, 'like', '%'.$value);
                    break;
            }
        });
    }

    protected function applyNumericCondition(Builder $query, string $field, string $operator, string $value, string $method): void
    {
        $query->$method(function (Builder $q) use ($field, $operator, $value) {
            switch ($operator) {
                case 'eq':
                    $q->where($field, '=', (float) $value);
                    break;
                case 'neq':
                    $q->where($field, '!=', (float) $value);
                    break;
                case 'gt':
                    $q->where($field, '>', (float) $value);
                    break;
                case 'lt':
                    $q->where($field, '<', (float) $value);
                    break;
                case 'gte':
                    $q->where($field, '>=', (float) $value);
                    break;
                case 'lte':
                    $q->where($field, '<=', (float) $value);
                    break;
            }
        });
    }

    protected function applyForeignKeyCondition(Builder $query, string $field, string $operator, string $value, string $method): void
    {
        $query->$method(function (Builder $q) use ($field, $operator, $value) {
            switch ($operator) {
                case 'equals':
                case 'eq':
                    $q->where($field, '=', (int) $value);
                    break;
                case 'not_equals':
                case 'neq':
                    $q->where($field, '!=', (int) $value);
                    break;
                case 'contains_any':
                    $values = array_map('intval', explode(',', $value));
                    $q->whereIn($field, $values);
                    break;
                case 'contains_all':
                    $values = array_map('intval', explode(',', $value));
                    foreach ($values as $v) {
                        $q->where($field, '=', $v);
                    }
                    break;
            }
        });
    }

    protected function applyBrandCondition(Builder $query, string $operator, string $value, string $method): void
    {
        $query->$method(function (Builder $q) use ($operator, $value) {
            switch ($operator) {
                case 'equals':
                case 'eq':
                    $brandId = (int) $value;
                    $q->where(fn ($b) => $b->where('brand_id', $brandId)
                        ->orWhereHas('brands', fn ($br) => $br->where('brands.id', $brandId)));
                    break;
                case 'not_equals':
                case 'neq':
                    $brandId = (int) $value;
                    $q->where('brand_id', '!=', $brandId)
                        ->whereDoesntHave('brands', fn ($br) => $br->where('brands.id', $brandId));
                    break;
                case 'contains_any':
                    $values = array_map('intval', explode(',', $value));
                    $q->where(function ($b) use ($values) {
                        $b->whereIn('brand_id', $values)
                            ->orWhereHas('brands', fn ($br) => $br->whereIn('brands.id', $values));
                    });
                    break;
                case 'contains_all':
                    $values = array_map('intval', explode(',', $value));
                    foreach ($values as $v) {
                        $brandId = (int) $v;
                        $q->where(function ($b) use ($brandId) {
                            $b->where('brand_id', $brandId)
                                ->orWhereHas('brands', fn ($br) => $br->where('brands.id', $brandId));
                        });
                    }
                    break;
            }
        });
    }

    protected function applyCategoryCondition(Builder $query, string $operator, string $value, string $method): void
    {
        $query->$method(function (Builder $q) use ($operator, $value) {
            switch ($operator) {
                case 'equals':
                case 'eq':
                    $q->whereHas('categories', fn ($c) => $c->where('categories.id', (int) $value));
                    break;
                case 'not_equals':
                case 'neq':
                    $q->whereDoesntHave('categories', fn ($c) => $c->where('categories.id', (int) $value));
                    break;
                case 'contains_any':
                    $values = array_map('intval', explode(',', $value));
                    $q->whereHas('categories', fn ($c) => $c->whereIn('categories.id', $values));
                    break;
                case 'contains_all':
                    $values = array_map('intval', explode(',', $value));
                    foreach ($values as $v) {
                        $q->whereHas('categories', fn ($c) => $c->where('categories.id', $v));
                    }
                    break;
            }
        });
    }

    protected function applyTagCondition(Builder $query, string $operator, string $value, string $method): void
    {
        $query->$method(function (Builder $q) use ($operator, $value) {
            switch ($operator) {
                case 'equals':
                case 'contains':
                    $q->whereHas('tags', function ($q) use ($value) {
                        $q->where('tags.name', '=', $value);
                    });
                    break;
                case 'not_equals':
                    $q->whereDoesntHave('tags', function ($q) use ($value) {
                        $q->where('tags.name', '=', $value);
                    });
                    break;
                case 'contains_any':
                    $values = array_map('trim', explode(',', $value));
                    $q->whereHas('tags', function ($q) use ($values) {
                        $q->whereIn('tags.name', $values);
                    });
                    break;
                case 'contains_all':
                    $values = array_map('trim', explode(',', $value));
                    foreach ($values as $tagName) {
                        $q->whereHas('tags', function ($q) use ($tagName) {
                            $q->where('tags.name', '=', $tagName);
                        });
                    }
                    break;
            }
        });
    }

    public static function conditionFields(): array
    {
        return [
            'title' => 'Product Title',
            'product_type' => 'Product Type',
            'brand' => 'Brand',
            'category' => 'Category',
            'tag' => 'Tag',
            'price' => 'Price',
            'sale_price' => 'Sale Price',
            'stock' => 'Stock Quantity',
            'status' => 'Product Status',
            'sku' => 'SKU',
        ];
    }

    public static function textOperators(): array
    {
        return [
            'equals' => 'Equals',
            'not_equals' => 'Not equals',
            'contains' => 'Contains',
            'starts_with' => 'Starts with',
            'ends_with' => 'Ends with',
        ];
    }

    public static function numericOperators(): array
    {
        return [
            'eq' => '=',
            'neq' => '!=',
            'gt' => '>',
            'lt' => '<',
            'gte' => '>=',
            'lte' => '<=',
        ];
    }

    public static function listOperators(): array
    {
        return [
            'contains_any' => 'Contains any',
            'contains_all' => 'Contains all',
        ];
    }

    public static function operatorsForField(string $field): array
    {
        $textFields = ['title', 'product_type', 'sku', 'status'];
        $numericFields = ['price', 'sale_price', 'stock'];
        $fkFields = ['brand', 'category'];
        $tagField = ['tag'];

        if (in_array($field, $textFields)) {
            return self::textOperators();
        }

        if (in_array($field, $numericFields)) {
            return self::numericOperators();
        }

        if (in_array($field, $fkFields)) {
            return array_merge(self::textOperators(), self::listOperators());
        }

        if (in_array($field, $tagField)) {
            return array_merge(
                ['equals' => 'Equals', 'not_equals' => 'Not equals'],
                self::listOperators()
            );
        }

        return self::textOperators();
    }
}
