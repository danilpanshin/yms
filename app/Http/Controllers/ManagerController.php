<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingInvite;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\URL;

class ManagerController extends Controller
{
    public function index()
    {
        $suppliers = User::role('supplier')->get();
        return view('manager.index', compact('suppliers'));
    }

    public function send_invite(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:users,id',
        ]);

        $supplier = User::findOrFail($request->supplier_id);
        if (!$supplier->hasRole('supplier')) {
            return back()->with('error', 'Выбранный пользователь не является поставщиком');
        }

        // Генерируем JWT токен с временем действия
        $expiration = now()->addDays(config('app.booking_link_expire', 1));
        $token = $this->generateToken($supplier->id, $expiration);

        // Генерируем подписанный URL
        $signedUrl = URL::temporarySignedRoute(
            'supplier.claim.add_with_invite',
            $expiration,
            ['token' => $token]
        );

        // Отправляем письмо
        Mail::to($supplier->email)->send(new BookingInvite($signedUrl, $expiration));

        return back()->with('success', 'Приглашение отправлено поставщику!');
    }

    private function generateToken($userId, $expiration)
    {
        $key = config('app.key');
        $payload = [
            'user_id' => $userId,
            'exp' => $expiration->timestamp,
            'iss' => config('app.url')
        ];

        return JWT::encode($payload, $key, 'HS256');
    }
}