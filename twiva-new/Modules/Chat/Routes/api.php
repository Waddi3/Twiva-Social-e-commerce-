<?php

use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\Api\V1\ApiChatGroupController;
use Modules\Chat\Http\Controllers\Api\V1\ApiChatSettingsController;
use Modules\Chat\Http\Controllers\Api\V1\ApiTimeLineReportController;
use Modules\Chat\Http\Controllers\Api\V1\ApiTimelineController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::domain(config('app.api_url'))->group(function () {
    Route::middleware('auth:api')
        ->prefix('/v1/chat')->group(function () {
            Route::middleware('ChatActiveCommunityMiddleware')->group(function () {
                // timelines apis
                Route::get('timeline', [ApiTimelineController::class, 'index'])->name('api.chat.timelines.list');
                Route::get('timeline/pinned', [ApiTimelineController::class, 'pinned'])->name('api.chat.timelines.pinned');
                Route::post('/timeline/create', [ApiTimelineController::class, 'create'])->name('api.chat.timelines.create');
                Route::get('/timeline/{timeline}/show', [ApiTimelineController::class, 'show'])->name('api.chat.timelines.show');
                Route::get('/timeline/{timeline}/view', [ApiTimelineController::class, 'view'])->name('api.chat.timelines.view');
                Route::get('/timeline/{timeline}/like', [ApiTimelineController::class, 'like'])->name('api.chat.timelines.like');
                Route::post('/timeline/{timeline}/comment', [ApiTimelineController::class, 'comment'])->name('api.chat.timelines.add.comment');
                Route::get('/timeline/{timeline}/comments', [ApiTimelineController::class, 'comments'])->name('api.chat.timelines.comments.list');
                Route::get('/timeline/{timeline}/{comment}/replies', [ApiTimelineController::class, 'commentReplies'])->name('api.chat.timelines.comment.replies.list');
                // reason and report
                Route::get('/timeline/report/reasons', [ApiTimeLineReportController::class, 'reportReasons'])->name('api.timeline.report.reasons');
                Route::post('/timeline/{timeline}/report', [ApiTimeLineReportController::class, 'report'])->name('api.timeline.report');
                // chat groups apis
                Route::get('/groups', [ApiChatGroupController::class, 'groups'])->name('api.chat.groups.list');
                Route::get('/groups/{group}/members', [ApiChatGroupController::class, 'members'])->name('api.chat.groups.list.members');
                Route::get('/groups/{group}/show', [ApiChatGroupController::class, 'show'])->name('api.chat.groups.show');
                Route::get('/groups/{group}/posts', [ApiChatGroupController::class, 'getGroupPosts'])->name('api.chat.groups.posts');
            });
            //settings
            Route::get('/settings', [ApiChatSettingsController::class, 'settings'])->name('api.chat.settings.fetch');
            Route::post('/settings', [ApiChatSettingsController::class, 'update'])->name('api.chat.settings.update');

            Route::middleware('ChatAdminMiddleware')->group(function () {
                // admin apis
                Route::post('/groups/create', [ApiChatGroupController::class, 'create'])->name('api.chat.groups.create');
                Route::post('/groups/{group}/update', [ApiChatGroupController::class, 'update'])->name('api.chat.groups.update');
                Route::post('/groups/{group}/delete', [ApiChatGroupController::class, 'delete'])->name('api.chat.groups.delete');
                Route::post('/groups/sync-members', [ApiChatGroupController::class, 'syncMembers'])->name('api.chat.groups.sync.members');
                Route::post('/timeline/{timeline}/delete-restore', [ApiTimelineController::class, 'delete'])->name('api.chat.timelines.delete');
                Route::get('/timeline/{timeline}/pin', [ApiTimelineController::class, 'pin'])->name('api.chat.timelines.pin');
                Route::get('/timeline/reports', [ApiTimeLineReportController::class, 'reports'])->name('api.chat.reports.list');
                Route::get('/timeline/reports/{report}/show', [ApiTimeLineReportController::class, 'show'])->name('api.chat.reports.show');
                Route::get('/timeline/reports/{report}/status', [ApiTimeLineReportController::class, 'status'])->name('api.chat.reports.status');
                Route::get('/users', [ApiChatGroupController::class, 'users'])->name('api.chat.users')->withoutMiddleware('ChatAdminMiddleware');
                Route::get('/users/banned', [ApiChatGroupController::class, 'usersBanned'])->name('api.chat.users.banned');
                Route::get('/users/{user}/banned', [ApiChatGroupController::class, 'userBanned'])->name('api.chat.user.banned');
            });
        });
});
