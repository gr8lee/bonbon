<?php


use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\SecurityController;

Route::middleware('auth')->group(function () {
    Route::get('/account/security', [SecurityController::class, 'index'])->name('account.security');
    Route::put('/account/password', [SecurityController::class, 'updatePassword'])->name('account.password');
    Route::post('/account/logout-other-devices', [SecurityController::class, 'logoutOtherDevices'])->name('account.logout-others');
});

// Show Login / Register Page
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(auth()->user()->isAdmin() ? '/admin/dashboard' : '/store');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
    });

    Route::post('/register', function (Request $request) {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/store');
    })->name('register');
});

// Logout
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// ... your existing routes (like /, /store, /login, etc.) stay right here ...

// Admin-only routes (add this at the bottom of routes/web.php)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('admin.dashboard');

    Route::get('/devices', function () {
        $activeSessions = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select('sessions.*', 'users.name', 'users.email', 'users.role')
            ->orderByDesc('sessions.last_activity')
            ->get()
            ->map(function ($s) {
                $s->last_active = Carbon::createFromTimestamp($s->last_activity)->diffForHumans();
                return $s;
            });

        $loginLogs = DB::table('login_histories')
            ->join('users', 'login_histories.user_id', '=', 'users.id')
            ->select('login_histories.*', 'users.name', 'users.email')
            ->orderByDesc('login_histories.logged_in_at')
            ->limit(50)
            ->get();

        return view('admin.devices', compact('activeSessions', 'loginLogs'));
    })->name('admin.devices');

    Route::delete('/devices/{sessionId}', function (string $sessionId) {
        DB::table('sessions')->where('id', $sessionId)->delete();
        return back()->with('status', 'Device session terminated.');
    })->name('admin.devices.revoke');
});

Route::get('/', function () {
    return view('index');
});
Route::get('/store', function () {
    return view('store');
});
