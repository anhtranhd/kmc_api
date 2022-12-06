<?php

namespace App\Models;

use App\Libs\EasyAR\EasyARClientSdkCRS;

class EasyAR
{
    private $easyARClient;

    public function __construct()
    {
        $apiKey = config('easy_ar.api_key');
        $apiSecret = config('easy_ar.api_secret');
        $crsAppId = config('easy_ar.crs_app_id');
        $crsCloudUrl = config('easy_ar.crs_cloud_url');

        $this->easyARClient = new EasyARClientSdkCRS($apiKey, $apiSecret, $crsAppId, $crsCloudUrl);
    }

    public function uploadTarget(ArTarget $arTarget)
    {
        $params = [
            'name' => $arTarget->name,
            'active' => '0',
            'size' => '1111',
            'meta' => base64_encode(''),
            'image' => base64_encode(file_get_contents(public_path($arTarget->image))),
        ];

        return $this->easyARClient->targetAdd($params);
    }

    public function updateTarget(ArTarget $arTarget, $data)
    {
        $data = array_merge(['status' => 1, 'type' => 'content'], $data);
        $params = [
            'active' => $data['status'] ? '1' : '0',
            'meta' => base64_encode(json_encode($data['type']))
        ];

        return $this->easyARClient->targetUpdate($arTarget->easy_ar_id, $params);
    }

    public function destroy(string $easyArID)
    {
        return $this->easyARClient->delete($easyArID);
    }
}
