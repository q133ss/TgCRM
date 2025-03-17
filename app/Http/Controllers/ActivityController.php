<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityController\StoreRequest;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    private User $user;

    public function __construct()
    {
        if(isset(request()->uid)){
            $this->user = User::where('telegram_id', request()->uid)->firstOrFail();
        }else{
            if(auth()->check()) {
                $this->user = auth()->user();
            }else{
                abort(401);
            }
        }
    }
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $this->user?->id;
        Activity::create($data);
        return response()->json(['message' => 'true']);
    }
}
