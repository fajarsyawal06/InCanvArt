<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\UpgradeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| VERIFY EMAIL
|--------------------------------------------------------------------------
*/

Route::get('/verify', function (Request $request) {
    $user = \App\Models\User::where('email', $request->email)
        ->where('token', $request->token)
        ->first();

    if ($user) {
        $user->update([
            'status' => 'aktif',
            'token'  => null,
        ]);

        return redirect('/login')->with('success', 'Akun berhasil diverifikasi!');
    }

    return redirect('/login')->with('error', 'Token tidak valid.');
});

/*
|--------------------------------------------------------------------------
| TESTING
|--------------------------------------------------------------------------
*/
Route::get('/testOutput', fn() => view('testOutput'));

/*
|--------------------------------------------------------------------------
| DASHBOARD SESUAI ROLE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'pengunjung'])->group(function () {
    Route::get('/',  fn() => view('dashboard'))
        ->name('dashboard');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admindashboard', fn() => view('admin.index'))
        ->name('admin');


    Route::get('/statistic', [StatisticController::class, 'index'])
        ->name('statistics.index');

    Route::get('/statistic/export/pdf', [StatisticController::class, 'exportPdf'])
        ->name('statistic.export');

    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('categories.index');

    Route::get('/categories/create', [CategoryController::class, 'create'])
        ->name('categories.create');

    Route::post('/categories/store', [CategoryController::class, 'store'])
        ->name('categories.store');

    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])
        ->name('categories.edit');

    Route::put('/categories/{id}/update', [CategoryController::class, 'update'])
        ->name('categories.update');

    Route::delete('categories/{id}', [CategoryController::class, 'destroy'])
        ->name('categories.destroy');

    Route::get('/moderations', [ModerationController::class, 'index'])
        ->name('moderations.index');

    Route::get('/moderations/{moderation}', [ModerationController::class, 'show'])
        ->name('moderations.show');

    Route::post('/moderations', [ModerationController::class, 'store'])
        ->name('moderations.store');

    Route::put('/moderations/{moderation}', [ModerationController::class, 'update'])
        ->name('moderations.update');

    Route::delete('/moderations/{moderation}', [ModerationController::class, 'destroy'])
        ->name('moderations.destroy');

    Route::post('/report/artwork/{artwork}', [ModerationController::class, 'reportArtwork'])
        ->name('artworks.report');

    Route::post('/report/comment/{comment}', [ModerationController::class, 'reportComment'])
        ->name('comments.report');

    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('users.show');

    Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])
        ->name('users.deactivate');

    Route::post('/users/{user}/activate', [UserController::class, 'activate'])
        ->name('users.activate');
});

/*
|--------------------------------------------------------------------------
| AUTH (GUEST ONLY)
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::middleware('guest')->group(function () {

    // Register
    Route::get('/register', [AuthController::class, 'registerLihat'])
        ->name('register.lihat');
    Route::post('/register/submit', [AuthController::class, 'registerSubmit'])
        ->name('register.submit');

    // Login
    Route::get('/login', [AuthController::class, 'loginLihat'])
        ->name('login.lihat');
    Route::post('/login/submit', [AuthController::class, 'loginSubmit'])
        ->name('login.submit');
});

/*
|--------------------------------------------------------------------------
| ARTWORKS (INDEX PUBLIK)
|--------------------------------------------------------------------------
*/
Route::get('/artworks', [ArtworkController::class, 'index'])
    ->name('artworks.index');

/*
|--------------------------------------------------------------------------
| ARTWORK — KHUSUS SENIMAN (auth + role seniman)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'seniman'])->group(function () {

    // Form tambah karya
    Route::get('/artworks/create', [ArtworkController::class, 'create'])
        ->name('artworks.create');

    // Simpan karya baru
    Route::post('/artworks', [ArtworkController::class, 'store'])
        ->name('artworks.store');

    // Form edit karya
    Route::get('/artworks/{artwork}/edit', [ArtworkController::class, 'edit'])
        ->name('artworks.edit');

    // Update karya
    Route::put('/artworks/{artwork}', [ArtworkController::class, 'update'])
        ->name('artworks.update');

    // Hapus karya
    Route::delete('/artworks/{artwork}', [ArtworkController::class, 'destroy'])
        ->name('artworks.destroy');

    // Statistik karya (opsional: hanya bisa diakses seniman)
    Route::get('/artworks/{artwork}/statistic', [ArtworkController::class, 'statistic'])
        ->name('artworks.statistic');
});

/*
|--------------------------------------------------------------------------
| ROUTE LOGIN UMUM (PENGUNJUNG & SENIMAN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |----------------------------- INTERAKSI ARTWORK -------------------------
    */
    Route::post('/artworks/{artwork}/like', [LikeController::class, 'toggle'])
        ->name('artworks.like');

    Route::post('/artworks/{artwork}/bookmark', [FavoriteController::class, 'toggle'])
        ->name('artworks.bookmark');

    Route::post('/artwork/{artwork}/share', [ShareController::class, 'store'])
        ->name('artworks.share');

    Route::post('/artworks/{artwork}/comments', [CommentController::class, 'store'])
        ->name('comments.store');

    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');

    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])
        ->name('users.follow');

    /*
    |----------------------------- PROFILE -----------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profiles.index');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profiles.edit');

    Route::put('/profile/update', [ProfileController::class, 'update'])
        ->name('profiles.update');

    // Profil user lain (tetap butuh login)
    Route::get('/profile/{user}', [ProfileController::class, 'show'])
        ->name('profiles.show');

    /*
    |----------------------------- FAVORITE & STATISTICS ----------------------
    */
    Route::get('/favorite', [FavoriteController::class, 'index'])
        ->name('favorite');

    /*
    |----------------------------- SEARCH ------------------------------------
    */
    Route::get('/search/users', [SearchController::class, 'liveUsers'])
        ->name('search.users');

    Route::get('/search', [SearchController::class, 'index'])
        ->name('search.index');

    /*
    |----------------------------- UPGRADE AKUN -------------------------------
    */
    Route::get('/upgrade', [UpgradeController::class, 'showForm'])
        ->name('upgrade.akun');

    Route::post('/upgrade/submit', [UpgradeController::class, 'store'])
        ->name('upgrade.submit');
});

/*
|--------------------------------------------------------------------------
| DETAIL ARTWORK (PUBLIK) — HARUS PALING TERAKHIR
|--------------------------------------------------------------------------
|
| Penting: diletakkan setelah semua route /artworks/... lain
| supaya /artworks/create, /artworks/{id}/edit, dll.
| tidak tertangkap oleh {artwork}.
|
*/
Route::get('/artworks/{artwork}', [ArtworkController::class, 'show'])
    ->name('artworks.show');
