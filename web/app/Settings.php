<?php

namespace App;

class Settings
{
    public static $general = [
        'brand_name' => 'Phyre Panel',
        'master_domain' => '',
        'master_email' => '',
        'master_country' => '',
        'master_locality' => '',
        'organization_name' => '',
    ];

    public static function general()
    {
        $settings = setting('general');
        if (! empty($settings)) {
            foreach ($settings as $key => $value) {
                if (isset(self::$general[$key])) {
                    self::$general[$key] = $value;
                }
            }
        }

        return self::$general;
    }
}
