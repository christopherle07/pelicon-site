<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/download', [DownloadController::class, 'index'])->name('download.index');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/privacy-policy', 'pages.privacy')->name('privacy');
Route::view('/terms-of-service', 'pages.terms')->name('terms');
Route::view('/faq', 'pages.faq')->name('faq');
Route::view('/app-documentation', 'pages.app-documentation')->name('docs.app');
Route::view('/plugin-documentation', 'pages.plugin-documentation')->name('docs.plugins');
