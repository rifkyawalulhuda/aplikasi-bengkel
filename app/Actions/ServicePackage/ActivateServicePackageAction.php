<?php

namespace App\Actions\ServicePackage;

use App\Models\ServicePackage;

class ActivateServicePackageAction
{
    public function handle(ServicePackage $servicePackage): ServicePackage
    {
        $servicePackage->forceFill([
            'is_active' => true,
        ])->save();

        return $servicePackage->refresh();
    }
}
