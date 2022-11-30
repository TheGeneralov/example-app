<?php

namespace App\Services\Bitrix24;

use App\DTO\Shop\RegistrationDto;
use Illuminate\Support\Facades\Http;

class Bitrix24Service
{
    private string $domain;
    private string $secretLink;
    private string $contactId;
    private string $bitrixRestUrl;
    private string $bitrixUFCity;
    private string $bitrixUFCityId;
    private string $bitrixSourceId;

    public function __construct()
    {
        $this->domain = config('services.bitrix24.domain');
        $this->secretLink = config('services.bitrix24.secret_link');
        $this->bitrixUFCity = config('services.bitrix24.uf_city');
        $this->bitrixUFCityId = config('services.bitrix24.uf_city_id');
        $this->bitrixSourceId = config('services.bitrix24.source_id');
        $this->bitrixRestUrl = 'https://' . $this->domain . '/rest/' . $this->secretLink;
    }

    public function sendRegisterData(RegistrationDto $dto): void
    {
        $this->sendContact([
            'NAME' => $dto->getUsername(),
            'EMAIL' => [
                'n0' => [
                    'VALUE' => $dto->getEmail(),
                    'VALUE_TYPE' => 'WORK',
                ],
            ],
            'PHONE' => [
                'n0' => [
                    'VALUE' => $dto->getPhone(),
                    'VALUE_TYPE' => 'WORK',
                ],
            ],
        ]);
        $utm = $dto->getUtm();
        $this->sendDeal(
            [
                'TITLE' => 'Регистрация ' . $dto->getShopName(),
                'CONTACT_ID' => $this->contactId,
                'COMMENTS' => 'Название заведения: ' . $dto->getShopName(),
                'SOURCE_ID' => $this->bitrixSourceId,
                'SOURCE_DESCRIPTION' => $_SERVER['HTTP_REFERER'],
                'UTM_SOURCE' => $utm['utm_source'],
                'UTM_MEDIUM' => $utm['utm_medium'],
                'UTM_CAMPAIGN' => $utm['utm_campaign'],
                'UTM_CONTENT' => $utm['utm_content'],
                'UTM_TERM' => $utm['utm_term'],
            ]
        );
    }

    public function sendCallBack($fields): void
    {
        if ($resultFindCity = $this->findCity($fields['city'])) {
            $cityId = $resultFindCity['ID'];
            $comment = '';
        } else {
            $cityId = config('services.bitrix24.def_city_id');
            $comment = 'Город: ' . $fields['city'];
        }
        $this->sendContact([
            'NAME' => $fields['name'],
            'EMAIL' => [
                'n0' => [
                    'VALUE' => $fields['email'],
                    'VALUE_TYPE' => 'WORK',
                ],
            ],
            'PHONE' => [
                'n0' => [
                    'VALUE' => $fields['phone'],
                    'VALUE_TYPE' => 'WORK',
                ],
            ],
        ]);
        $this->sendDeal(
            [
                'TITLE' => 'Заявка с сайта от ' . $fields['name'],
                'CONTACT_ID' => $this->contactId,
                'COMMENTS' => $comment,
                $this->bitrixUFCity => $cityId,
                'SOURCE_ID' => $this->bitrixSourceId,
                'SOURCE_DESCRIPTION' => $fields['url'],
                'UTM_SOURCE' => $fields['utm_source'],
                'UTM_MEDIUM' => $fields['utm_medium'],
                'UTM_CAMPAIGN' => $fields['utm_campaign'],
                'UTM_CONTENT' => $fields['utm_content'],
                'UTM_TERM' => $fields['utm_term'],
            ]
        );
    }

    private function sendContact($fields): void
    {
        $response = Http::get($this->bitrixRestUrl . 'crm.contact.add.json', [
            'fields' => $fields,
            'params' => ['REGISTER_SONET_EVENT' => 'Y'],
        ]);

        $result = $response->json();
        $this->contactId = $result['result'];
    }

    private function sendDeal($fields): void
    {
        Http::get($this->bitrixRestUrl . 'crm.deal.add.json', [
            'fields' => $fields,
            'params' => ['REGISTER_SONET_EVENT' => 'Y'],
        ]);
    }

    private function findCity($cityName)
    {
        $resultCity = $this->getListUF($this->bitrixUFCityId);
        foreach ($resultCity['LIST'] as $city) {
            if ($city['VALUE'] == $cityName) {
                return $city;
            }
        }

        return false;
    }

    private function getListUF($idUF)
    {
        $response = Http::get($this->bitrixRestUrl . 'crm.deal.userfield.get.json', [
            'ID' => $idUF,
        ]);

        $result = $response->json();

        return $result['result'];
    }
}
