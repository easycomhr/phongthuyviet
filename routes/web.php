<?php

declare(strict_types=1);

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\BabyNamingController;
use App\Http\Controllers\CompatibilityController;
use App\Http\Controllers\GoodDayController;
use App\Http\Controllers\HouseDirectionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrayerController;
use App\Http\Controllers\TuViController;
use App\Http\Controllers\WeddingDateController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::get('/tra-cuu', [HomeController::class, 'search'])->name('search.index');
Route::get('/lich-hom-nay', [CalendarController::class, 'today'])->name('calendar.today');
Route::get('/tra-cuu-ngay-tot', [GoodDayController::class, 'index'])->name('gooddays.index');
Route::get('/tra-ngay-tot', [GoodDayController::class, 'index']);
Route::get('/xem-tuoi', [CompatibilityController::class, 'index'])->name('age.index');
Route::post('/xem-tuoi/ket-qua', [CompatibilityController::class, 'result'])->name('age.result');
Route::get('/huong-nha', [HouseDirectionController::class, 'index'])->name('direction.index');
Route::post('/huong-nha/ket-qua', [HouseDirectionController::class, 'result'])->name('direction.result');
Route::get('/tu-vi', [TuViController::class, 'index'])->name('tuvi.index');
Route::post('/tu-vi/ket-qua', [TuViController::class, 'result'])->name('tuvi.result');
Route::get('/ngay-cuoi', [WeddingDateController::class, 'index'])->name('wedding.index');
Route::post('/ngay-cuoi/ket-qua', [WeddingDateController::class, 'result'])->name('wedding.result');
Route::get('/ngay-cuoi/pdf', [WeddingDateController::class, 'exportPdf'])->name('wedding.pdf');
Route::get('/dat-ten-cho-con', [BabyNamingController::class, 'index'])->name('naming.index');
Route::post('/dat-ten-cho-con/ket-qua', [BabyNamingController::class, 'result'])->name('naming.result');
Route::get('/van-khan', [PrayerController::class, 'index'])->name('prayers.index');
Route::get('/van-khan/{slug}/pdf', [PrayerController::class, 'exportPdf'])->name('prayers.pdf');
Route::get('/van-khan/{slug}', [PrayerController::class, 'show'])->name('prayers.show');
