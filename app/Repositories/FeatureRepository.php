<?php

namespace App\Repositories;

use App\Feature;
use App\Utils\FeaturesEnum;
use Carbon\Carbon;

class FeatureRepository
{

    public function isFeatureActive(FeaturesEnum $feature){
        return Feature::where('feature', $feature->value)->where('active_at', '<=', Carbon::now())->exists();
    }
}
