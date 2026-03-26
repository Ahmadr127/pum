<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    /**
     * Handle SSO Login from Main SSO Token.
     */
    public function loginViaToken(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
        ]);

        $ssoBaseUrl = env('SSO_BASE_URL', 'http://127.0.0.1:8000'); // the main-sso URL

        try {
            // Verify token with main-sso
            $response = Http::withToken($request->access_token)
                ->get("{$ssoBaseUrl}/api/user");

            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid SSO token or SSO server unreachable.',
                    'details' => $response->body()
                ], 401);
            }

            $ssoUser = $response->json();
            // main-sso wraps data in {status, data: {...}}
            $ssoUserData = $ssoUser['data'] ?? $ssoUser;

            // Validasi: NIK wajib ada
            if (empty($ssoUserData['nik'])) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'SSO user does not have a NIK. Cannot authenticate.',
                ], 422);
            }

            $nik = trim((string) $ssoUserData['nik']);
            $name = trim((string) ($ssoUserData['name'] ?? 'User SSO'));
            $email = isset($ssoUserData['email']) ? trim((string) $ssoUserData['email']) : null;
            $username = isset($ssoUserData['username']) ? trim((string) $ssoUserData['username']) : null;

            // Permanent resolution strategy to avoid duplicate local users:
            // 1) by nik (canonical), 2) fallback claim by email, 3) fallback claim by username.
            $user = User::where('nik', $nik)->first();
            $resolvedBy = 'nik';

            if (!$user && $email) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $resolvedBy = 'email';
                }
            }

            if (!$user && $username) {
                $user = User::where('username', $username)->first();
                if ($user) {
                    $resolvedBy = 'username';
                }
            }

            if ($user) {
                // Bind legacy local account to current NIK so future logins are stable.
                $user->update([
                    'nik'      => $nik,
                    'name'     => $name,
                    'email'    => $email ?: $user->email,
                    'username' => $username ?: $user->username,
                ]);
            } else {
                $resolvedBy = 'create';

                // Buat user baru
                $user = User::create([
                    'nik'      => $nik,
                    'name'     => $name,
                    // users.email is required+unique; provide deterministic fallback if missing.
                    'email'    => $email ?: ('sso+' . $nik . '@local.invalid'),
                    'username' => $username ?: ('user_' . $nik),
                    'password' => bcrypt(Str::random(32)),
                ]);
            }

            Log::info('SSO user resolution (pum)', [
                'nik' => $nik,
                'resolved_by' => $resolvedBy,
                'user_id' => $user->id,
                'email' => $user->email,
                'username' => $user->username,
            ]);

            // Generate a local Sanctum token for the mobile app to use with pum API
            $token = $user->createToken('PUM API Token')->plainTextToken;

            // Gather permissions for the user
            $permissions = $user->role
                ? $user->role->permissions->pluck('name')->toArray()
                : [];

            return response()->json([
                'status'  => 'success',
                'message' => 'SSO Login successful',
                'data'    => [
                    'user' => [
                        'id'       => $user->id,
                        'name'     => $user->name,
                        'email'    => $user->email,
                        'username' => $user->username,
                        'nik'      => $user->nik,
                        'role'     => $user->role?->name,
                    ],
                    'permissions'  => $permissions,
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                ]
            ], 200);

        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'SSO server is unreachable. Connection refused.',
                'error'   => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred during SSO authentication.',
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ], 500);
        }
    }
}
