<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/download', [DownloadController::class, 'index'])->name('download.index');
Route::get('/download/{platform}', [DownloadController::class, 'show'])->name('download.show');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/licensing', 'pages.licensing')->name('licensing');
Route::view('/privacy-policy', 'pages.privacy')->name('privacy');
Route::view('/terms-of-service', 'pages.terms')->name('terms');
Route::view('/faq', 'pages.faq')->name('faq');
Route::view('/app-documentation', 'pages.app-documentation')->name('docs.app');
Route::view('/plugin-documentation', 'pages.plugin-documentation')->name('docs.plugins');
Route::view('/developer-policies', 'pages.developer-policies')->name('developer.policies');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{announcement:slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/users/{user:name}', [UserProfileController::class, 'show'])->name('users.show');
Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/{category:slug}', [ForumController::class, 'show'])->name('forum.show');
Route::get('/forum/{category:slug}/{thread:slug}', [ForumController::class, 'showThread'])->name('forum.threads.show');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
])->group(function () {
    Route::redirect('/settings', '/user/profile')->name('settings');

    Route::post('/news/{announcement:slug}/comments', [NewsController::class, 'storeComment'])->name('news.comments.store');
    Route::delete('/news/{announcement:slug}/comments/{comment}', [NewsController::class, 'destroyComment'])->name('news.comments.destroy');
    Route::post('/news/{announcement:slug}/reactions', [NewsController::class, 'react'])->name('news.react');

    Route::post('/forum/{category:slug}/threads', [ForumController::class, 'storeThread'])->name('forum.threads.store');
    Route::post('/forum/{category:slug}/{thread:slug}/lock', [ForumController::class, 'toggleThreadLock'])->name('forum.threads.lock');
    Route::delete('/forum/{category:slug}/{thread:slug}', [ForumController::class, 'destroyThread'])->name('forum.threads.destroy');
    Route::post('/forum/{category:slug}/{thread:slug}/replies', [ForumController::class, 'storeReply'])->name('forum.replies.store');
    Route::delete('/forum/{category:slug}/{thread:slug}/replies/{reply}', [ForumController::class, 'destroyReply'])->name('forum.replies.destroy');
    Route::post('/forum/{category:slug}/{thread:slug}/reactions', [ForumController::class, 'reactToThread'])->name('forum.threads.react');
    Route::post('/forum/{category:slug}/{thread:slug}/replies/{reply}/reactions', [ForumController::class, 'reactToReply'])->name('forum.replies.react');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'staff',
    'staff.2fa',
])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::middleware('admin')->prefix('news')->name('news.')->group(function (): void {
        Route::get('/', [AdminAnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [AdminAnnouncementController::class, 'create'])->name('create');
        Route::post('/', [AdminAnnouncementController::class, 'store'])->name('store');
        Route::get('/{announcement}/edit', [AdminAnnouncementController::class, 'edit'])->name('edit');
        Route::put('/{announcement}', [AdminAnnouncementController::class, 'update'])->name('update');
        Route::delete('/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('destroy');
    });
});
