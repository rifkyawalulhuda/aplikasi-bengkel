<?php

namespace App\Actions\CustomServiceItem;

use App\Models\CustomServiceItem;
use Illuminate\Support\Facades\DB;

class DeleteCustomServiceItemAction
{
    public function handle(CustomServiceItem $customServiceItem): void
    {
        DB::transaction(function () use ($customServiceItem): void {
            $customServiceItem->delete();
        });
    }
}
