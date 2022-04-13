<?php

namespace App\Utils;

use Exception;

class Config {
    const CONFIG_FILE_PATH = "config/config.json";

    public function getConfigByKey(string $key): ?string {
        try {
            $confing_json = json_decode(file_get_contents(self::CONFIG_FILE_PATH), true);
            $value = $confing_json[$key];
        } catch(Exception $e) {
            $value = null;
        }

        return $value;
    }

    public function setConfigByKey(string $key, $value): void {
        try {
            $confing_json = json_decode(file_get_contents(self::CONFIG_FILE_PATH), true);
            $confing_json[$key] = $value;
            file_put_contents(self::CONFIG_FILE_PATH, json_encode($confing_json, JSON_PRETTY_PRINT));
        } catch(Exception $e) {

        }
    }
}