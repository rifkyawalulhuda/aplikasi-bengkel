<?php

namespace App\Actions\ServicePackage;

use App\Models\ServicePackage;

class DeactivateServicePackageAction
{
    public function handle(ServicePackage $servicePackage): ServicePackage
    {
        $servicePackage->forceFill([
            'is_active' => false,
        ])->save();

        return $servicePackage->refresh();
    }
}
