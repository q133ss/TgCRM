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
        if (isset($update['message']) || isset($update['edited_message'])) {
            $user = (new UserService())->checkAndCreate($update['message'] ?? $update['edited_message']);
            (new TelegramService())->processMessage($update, $user);
        }

        return response('OK', 200);
    }
}
