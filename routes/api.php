<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ModeratorController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\PromoCodeController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TextController;
use App\Http\Controllers\Admin\WordsController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/




Route::prefix('admin')->group(function () {
Route::post('login', [LoginController::class, 'login']);
    Route::group(['middleware' => ['auth:api']], function () {
            Route::post('logout', [LoginController::class, 'logout']);
            Route::post('create_moderator', [ModeratorController::class, 'create_moderator']);
            Route::post('update_moderator', [ModeratorController::class, 'update_moderator']);
            Route::get('single_page_moderator', [ModeratorController::class, 'single_page_moderator']);
            Route::get('get_all_moderators', [ModeratorController::class, 'get_all_moderators']);

            Route::post('create_agent', [AgentController::class,'create_agent']);
            Route::post('update_agent', [AgentController::class,'update_agent']);
            Route::get('single_page_agent', [AgentController::class,'single_page_agent']);
            Route::get('all_agents', [AgentController::class,'all_agents']);


            Route::post('create_promo_code', [PromoCodeController::class, 'create_promo_code']);
            Route::post('update_promo_code', [PromoCodeController::class, 'update_promo_code']);
            Route::get('single_page_promo_code', [PromoCodeController::class, 'single_page_promo_code']);
            Route::get('get_all_promo_codes', [PromoCodeController::class, 'get_all_promo_codes']);

            Route::get('all_levels', [LevelController::class, 'all_levels']);
            Route::get('single_page_level', [LevelController::class, 'single_page_level']);
            Route::post('update_level', [LevelController::class, 'update_level']);
            Route::post('create_level', [LevelController::class, 'create_level']);


            Route::post('create_category', [CategoryController::class, 'create_category']);
            Route::get('all_category', [CategoryController::class, 'all_category']);
            Route::get('single_page_category', [CategoryController::class, 'single_page_category']);
            Route::delete('delete_category', [CategoryController::class, 'delete_category']);

            Route::post('add_texts'  , [TextController::class, 'add_texts']);
            Route::post('update_text'  , [TextController::class, 'update_text']);
            Route::get('get_all_texts'  , [TextController::class, 'get_all_texts']);
            Route::get('single_page_text'  , [TextController::class, 'single_page_text']);

            Route::post('create_words', [WordsController::class, 'create_words']);
            Route::post('delete_word', [WordsController::class, 'delete_word']);
            Route::post('update_word', [WordsController::class, 'update_word']);
            Route::get('get_all_words', [WordsController::class, 'get_all_words']);
        });
    });
