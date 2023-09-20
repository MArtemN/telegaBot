<?php
namespace FoodBot\Bot;

class Settings
{
    private const SETTINGS = [
        "HOST" => "localhost",
        "MAX_PROTEIN" => 55,
    ];

    public function __get(string $key)
    {
        if (array_key_exists($key, Settings::SETTINGS)) {
            return Settings::SETTINGS[$key];
        } else {
            return null;
        }
    }
}