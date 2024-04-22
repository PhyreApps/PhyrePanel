<?php

namespace Tests\Feature\Api;

use App\Models\ApiKey;

trait ApiKeysTrait
{
    public function getApiKey()
    {
        $findApiKey = ApiKey::where('name', 'Unit Test API')->first();
        if ($findApiKey) {
            return $findApiKey;
        }
        $this->createApiKey();
        return $this->getApiKey();
    }

    public function createApiKey()
    {
        $apiKey = new ApiKey();
        $apiKey->name = 'Unit Test API';
        $apiKey->is_active = true;
        $apiKey->enable_whitelisted_ips = false;
        $apiKey->save();
    }
}
