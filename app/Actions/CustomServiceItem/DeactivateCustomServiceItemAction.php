<?php

namespace App\Actions\CustomServiceItem;

use App\Models\CustomServiceItem;

class DeactivateCustomServiceItemAction
{
    public function handle(CustomServiceItem $customServiceItem): CustomServiceItem
    {
        $customServiceItem->forceFill([
            'is_active' => false,
        ])->save();

        return $customServiceItem->refresh();
    }
}
