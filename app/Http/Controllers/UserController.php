<?php

namespace App\Http\Controllers;

use App\Services\Bitrix24\Bitrix24Service;
use App\Services\RegistrationService;

class UserController extends Controller
{
    private RegistrationService $registrationService;
    private MailService $mailService;
    private Bitrix24Service $bitrix24Service;

    public function registration(RegistrationRequest $request): JsonResponse
    {
        try {
            $registrationDto = $request->toDto();
            $this->registrationService->registration($registrationDto);
            $this->mailService->sendRegistrationMail($registrationDto);
            $this->bitrix24Service->sendRegisterData($registrationDto);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(
                ['success' => false, 'error' => $e->getMessage()],
                ResponseCode::UNAUTHORIZED
            );
        }
    }
}
