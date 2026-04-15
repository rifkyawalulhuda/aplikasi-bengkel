<?php

namespace App\Actions\ServicePackage;

use App\Models\ServicePackage;
use Illuminate\Support\Facades\DB;

class DeleteServicePackageAction
{
    public function handle(ServicePackage $servicePackage): void
    {
        DB::transaction(function () use ($servicePackage): void {
            $servicePackage->delete();
        });
    }
}
