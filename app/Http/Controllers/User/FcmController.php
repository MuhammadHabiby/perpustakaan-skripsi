<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreFcmTokenRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FcmController extends Controller
{
    public function store(StoreFcmTokenRequest $request): JsonResponse
    {
        $user = $request->user();
        $token = $request->validated()['fcm_token'];

        try {
            $user->fcm_token = $token;
            $user->save();

            Log::info("FCM token updated for User ID: {$user->id}");
            return response()->json(['message' => 'FCM token berhasil disimpan.']);
        } catch (\Exception $e) {
            Log::error("Error storing FCM token for User ID: {$user->id} - " . $e->getMessage());
            return response()->json(['message' => 'Gagal menyimpan FCM token.'], 500);
        }
    }
}
