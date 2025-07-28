<?php


if (!function_exists('custom_secure_url')) {
    function custom_secure_url($path = null, $parameters = [])
    {
        return app()->environment('stage', 'prod')
            ? url($path, $parameters, true) // HTTPS на проде
            : url($path, $parameters);      // HTTP на локалке
    }
}