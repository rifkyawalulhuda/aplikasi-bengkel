<?php

namespace App\Actions\ServicePackage;

use App\Models\ServicePackage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpsertServicePackageAction
{
    public function handle(array $attributes, ?ServicePackage $servicePackage = null): ServicePackage
    {
        return DB::transaction(function () use ($attributes, $servicePackage): ServicePackage {
            $servicePackage ??= new ServicePackage();

            $servicePackage->fill([
                'name' => $attributes['name'],
                'slug' => $this->uniqueSlug(
                    name: $attributes['name'],
                    ignoreId: $servicePackage->exists ? $servicePackage->id : null,
                ),
                'short_description' => blank($attributes['short_description'] ?? null) ? null : trim($attributes['short_description']),
                'description' => blank($attributes['description'] ?? null) ? null : trim($attributes['description']),
                'price' => $attributes['price'],
                'duration_estimate_minutes' => $attributes['duration_estimate_minutes'],
                'is_active' => $attributes['is_active'],
                'is_featured' => $attributes['is_featured'],
                'display_order' => $attributes['display_order'],
            ]);

            $servicePackage->save();

            $servicePackage->items()->delete();
            $servicePackage->items()->createMany(
                collect($attributes['items'])
                    ->values()
                    ->map(fn (array $item, int $index): array => [
                        'name' => trim($item['name']),
                        'description' => blank($item['description'] ?? null) ? null : trim($item['description']),
                        'display_order' => $index + 1,
                    ])
                    ->all(),
            );

            return $servicePackage->load('items');
        });
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug !== '' ? $baseSlug : 'paket-servis';
        $suffix = 1;

        while (
            ServicePackage::query()
                ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = ($baseSlug !== '' ? $baseSlug : 'paket-servis').'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
