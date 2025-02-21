<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use App\Services\UserService;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        // Получаем данные из запроса
        $update = json_decode($request->getContent(), true);

        if (isset($update['message'])) {
            $user = (new UserService())->checkAndCreate($update['message']);
            (new TelegramService())->processMessage($update['message'], $user);
        }

        return response('OK', 200);
    }
}
