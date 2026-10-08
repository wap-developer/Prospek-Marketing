<?php

use App\Http\Controllers\Admin\MasterDataController as AdminMasterDataController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    // Prospek — semua role authenticated boleh lihat (scope difilter di controller)
    Route::get('prospects', [ProspectController::class, 'index'])->name('prospects.index');
    Route::get('prospects/export', [ProspectController::class, 'export'])->name('prospects.export');
    Route::middleware('role:manager_marketing,super_admin')->post('prospects/lock-toggle', [ProspectController::class, 'toggleLock'])->name('prospects.lock-toggle');
    Route::middleware('role:cs,super_admin')->group(function () {
        Route::get('prospects/check-phone', [ProspectController::class, 'checkPhone'])->name('prospects.check-phone');
        Route::get('prospects/create', [ProspectController::class, 'create'])->name('prospects.create');
        Route::post('prospects', [ProspectController::class, 'store'])->name('prospects.store');
    });
    Route::get('prospects/{prospect}/edit', [ProspectController::class, 'edit'])->name('prospects.edit');
    Route::put('prospects/{prospect}', [ProspectController::class, 'update'])->name('prospects.update');
    Route::middleware('role:manager_marketing,super_admin')->delete('prospects/{prospect}', [ProspectController::class, 'destroy'])->name('prospects.destroy');

    // To-do (marketing only)
    Route::middleware('role:marketing')->prefix('todos')->name('todos.')->group(function () {
        Route::get('daily', [TodoController::class, 'daily'])->name('daily');
        Route::post('daily', [TodoController::class, 'storeDaily'])->name('daily.store');
        Route::delete('pdfs/{pdf}', [TodoController::class, 'destroyPdf'])->name('daily.pdf.destroy');
    });

    // Manager monitoring
    Route::middleware('role:manager_marketing,super_admin')->prefix('manager')->name('manager.')->group(function () {
        Route::get('todos', [ManagerController::class, 'todos'])->name('todos');
        Route::get('todos/export', [ManagerController::class, 'exportTodos'])->name('todos.export');
        Route::post('todos/export/start', [ManagerController::class, 'startExport'])->name('todos.export.start');
        Route::get('todos/export/status/{export}', [ManagerController::class, 'exportStatus'])->name('todos.export.status');
        Route::get('todos/export/download/{export}', [ManagerController::class, 'downloadExport'])->name('todos.export.download');
        Route::get('todos/export/recent', [ManagerController::class, 'recentExports'])->name('todos.export.recent');
        Route::post('todos/lock-toggle', [ManagerController::class, 'toggleTodoLock'])->name('todos.lock-toggle');
        Route::get('todos/{user}/data', [ManagerController::class, 'todoData'])->name('todos.data');
        Route::post('todos/{user}/update', [ManagerController::class, 'updateTodo'])->name('todos.update');
        Route::delete('todos/pdfs/{pdf}', [ManagerController::class, 'destroyPdf'])->name('todos.pdf.destroy');
    });

    // Admin (super_admin only)
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
        Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        Route::get('masters', [AdminMasterDataController::class, 'index'])->name('masters.index');
        Route::post('masters/{resource}', [AdminMasterDataController::class, 'store'])->name('masters.store');
        Route::put('masters/{resource}/{id}', [AdminMasterDataController::class, 'update'])->name('masters.update');
        Route::delete('masters/{resource}/{id}', [AdminMasterDataController::class, 'destroy'])->name('masters.destroy');
    });
});
