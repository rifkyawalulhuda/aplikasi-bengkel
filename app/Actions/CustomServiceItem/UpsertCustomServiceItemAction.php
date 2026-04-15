<?php

namespace App\Actions\CustomServiceItem;

use App\Models\CustomServiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpsertCustomServiceItemAction
{
    public function handle(array $attributes, ?CustomServiceItem $customServiceItem = null): CustomServiceItem
    {
        return DB::transaction(function () use ($attributes, $customServiceItem): CustomServiceItem {
            $customServiceItem ??= new CustomServiceItem();

            $customServiceItem->fill([
                'name' => $attributes['name'],
                'slug' => $this->uniqueSlug(
                    name: $attributes['name'],
                    ignoreId: $customServiceItem->exists ? $customServiceItem->id : null,
                ),
                'category' => trim($attributes['category']),
                'description' => blank($attributes['description'] ?? null) ? null : trim($attributes['description']),
                'price' => $attributes['price'],
                'unit_label' => blank($attributes['unit_label'] ?? null) ? null : trim($attributes['unit_label']),
                'is_active' => $attributes['is_active'],
                'display_order' => $attributes['display_order'],
            ]);

            $customServiceItem->save();

            return $customServiceItem->refresh();
        });
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug !== '' ? $baseSlug : 'item-servis-custom';
        $suffix = 1;

        while (
            CustomServiceItem::query()
                ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = ($baseSlug !== '' ? $baseSlug : 'item-servis-custom').'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
