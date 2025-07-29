<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;

class VerifyBookingToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->route('token');

        try {
            $key = config('app.key');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            $supplier = User::findOrFail($decoded->user_id);

            // Проверяем роль пользователя
            if (!$supplier->hasRole('supplier')) {
                abort(403, 'Пользователь не является поставщиком');
            }

            // Проверяем срок действия
            if (now()->timestamp > $decoded->exp) {
                abort(403, 'Ссылка истекла');
            }

            // Добавляем данные токена в запрос
            $request->merge(['token_data' => (array)$decoded]);

        } catch (ExpiredException $e) {
            abort(403, 'Ссылка истекла');
        } catch (\Exception $e) {
            abort(403, 'Неверная ссылка');
        }

        return $next($request);
    }
}