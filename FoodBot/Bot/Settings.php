<?php
namespace FoodBot\Bot;

class Settings
{
    private const SETTINGS = [
        "HOST" => "localhost",
        "USER_NAME" => "a0657865_foodPlan",
        "PASSWORD" => "0lPvxzG9",
        "DB_NAME" => "a0657865_foodPlan",
        "MAX_PROTEIN" => 55,
        "TOKEN" => "6169354773:AAGjAa7TeoBhG9e4sOUN-oCLLLIQLNkOXFQ",
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