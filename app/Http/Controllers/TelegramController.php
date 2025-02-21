<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        // Получаем данные из запроса
        $update = json_decode($request->getContent(), true);

        if (isset($update['message'])) {
            $this->processMessage($update['message']);
        }

        return response('OK', 200);
    }
}
