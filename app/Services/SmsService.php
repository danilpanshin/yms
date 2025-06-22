<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class SmsService
{
    /**
     * Валидация телефона
     *
     * @param $phone
     * @return string
     */
    private static function validatePhone($phone): string
    {
        $res = 'ok';
        if(!$phone){
            $res = 'не валидный номер';
        } else if(strlen($phone) != 11){
            $res = 'не валидный номер';
        } else if(strpos($phone, '4') === 2){
            $res = 'не мобильный номер';
        }

        return $res;
    }

    /**
     * Отправка смс
     *
     * @param $phone
     * @param $content
     * @return array
     */
    public static function send($phone, $content): array
    {
        $config = Config::get('app.sms');
        $res = [
            'send' => false,
            'result' => [],
            'errors' => [],
        ];
        $phone = self::normalizePhone($phone);
        $phone_origin = $phone;
        $validate_result = self::validatePhone($phone);
        if($validate_result == 'ok'){
            $res['send'] = true;
            $res['result'] = Http::post(self::preparePostUrl($phone, $config, $content))->body();
        } else {
            $res['errors'][] = $validate_result . $phone_origin;
        }

        activity('sms')
            ->event('send')
            ->causedBy(Auth::user())
            ->withProperties(['phone' => $phone, 'text' => $content, 'result' => $res])
            ->log('send sms');

        return $res;
    }

    /**
     * Подготовка урла для отправки смс
     *
     * @param $phone
     * @param $config
     * @param $content
     * @return string
     */
    private static function preparePostUrl($phone, $config, $content): string
    {
        # https://api.unisender.com/ru/api/sendSms?format=json&api_key=KEY&phone=PHONE1,PHONE2&sender=FROM&text=TEXT
        return $config['url']
            . '?format=' . $config['format']
            . '&api_key=' . $config['token']
            . '&phone=' . $phone
            . '&sender=' . $config['sender']
            . '&text=' . $content;
    }

    public static function normalizePhone($phone): array|string|null
    {
        // Удаляем все символы, кроме цифр
        $digitsOnly = preg_replace('/\D/', '', $phone);

        if(strlen($digitsOnly) == 10){
            $digitsOnly  = '7' . $digitsOnly;
        }

        // Если номер начинается с 8, заменяем его на 7
        if (str_starts_with($digitsOnly, '8')) {
            $digitsOnly = '7' . substr($digitsOnly, 1);
        }

        return $digitsOnly;
    }

}