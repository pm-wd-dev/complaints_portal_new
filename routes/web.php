<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminComplaintController;
use App\Http\Controllers\ComplaintController;
// use App\Http\Controllers\ResponseController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PublicComplaintController;
use App\Http\Controllers\PublicTrackingController;
use App\Http\Controllers\CastMemberController;
use App\Http\Controllers\GuestComplaintController;
use App\Http\Controllers\Admin\ComplaintResolutionController;
use App\Http\Controllers\AttachmentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/migrate', function () {
    Artisan::call('migrate', ['--force' => true]);
});

Route::get('/dbSeed', function () {
    Artisan::call('db:seed', ['--force' => true]);
});
// Public complaint routes
Route::get('/guest-complaints/create', [GuestComplaintController::class, 'create'])->name('public.complaints.create');
Route::post('/guest-complaints', [GuestComplaintController::class, 'store'])->name('public.complaints.store');
Route::get('/guest-complaints/track', [GuestComplaintController::class, 'showTrackForm'])->name('public.complaints.track-form');
Route::post('/guest-complaints/track', [GuestComplaintController::class, 'track'])->name('public.complaints.track');
Route::get('/complaint/{caseNumber}', [GuestComplaintController::class, 'viewComplaint'])->name('public.complaints.view');
Route::get('/respond/{caseNumber}', [GuestComplaintController::class, 'respondentAccess'])->name('public.respondent.access');
Route::post('/guest-complaints/upload-signature', [GuestComplaintController::class, 'uploadSignature'])->name('public.complaints.upload-signature');

// Guest OTP routes for accessing all complaints
Route::post('/guest/send-otp', [GuestComplaintController::class, 'sendOtp'])->name('guest.send-otp');
Route::post('/guest/verify-otp', [GuestComplaintController::class, 'verifyOtp'])->name('guest.verify-otp');
Route::get('/guest-complaints/api/{caseNumber}', [GuestComplaintController::class, 'getComplaintDetails'])->name('guest.complaint.api');

// Debug route for testing
Route::get('/debug-respondent', function() {
    return response()->json([
        'session_data' => session()->all(),
        'authenticated' => session('respondent_authenticated'),
        'user_id' => session('respondent_user_id')
    ]);
});

// Respondent Authentication Routes
Route::prefix('respondent')->group(function () {
    Route::get('/login', [App\Http\Controllers\RespondentController::class, 'showLoginForm'])->name('respondent.login');
    Route::post('/login', [App\Http\Controllers\RespondentController::class, 'login'])->name('respondent.login.submit');
    Route::get('/otp', [App\Http\Controllers\RespondentController::class, 'showOtpForm'])->name('respondent.otp');
    Route::post('/otp', [App\Http\Controllers\RespondentController::class, 'verifyOtp'])->name('respondent.otp.verify');
    Route::post('/logout', [App\Http\Controllers\RespondentController::class, 'logout'])->name('respondent.logout');
    
    // Protected respondent routes
    Route::middleware(['respondent.auth'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\RespondentController::class, 'dashboard'])->name('respondent.dashboard');
        Route::get('/complaint/{complaint}', [App\Http\Controllers\RespondentController::class, 'viewComplaint'])->name('respondent.complaint.view');
        Route::post('/complaint/{complaint}/respond', [App\Http\Controllers\RespondentController::class, 'submitResponse'])->name('respondent.complaint.respond');
        Route::get('/profile', [App\Http\Controllers\RespondentController::class, 'profile'])->name('respondent.profile');
        Route::put('/profile', [App\Http\Controllers\RespondentController::class, 'updateProfile'])->name('respondent.profile.update');
    });
});

// Lawyer Authentication Routes
Route::prefix('lawyer')->group(function () {
    Route::get('/login', [App\Http\Controllers\LawyerController::class, 'showLoginForm'])->name('lawyer.login');
    Route::post('/login', [App\Http\Controllers\LawyerController::class, 'login'])->name('lawyer.login.submit');
    Route::get('/otp', [App\Http\Controllers\LawyerController::class, 'showOtpForm'])->name('lawyer.otp');
    Route::post('/otp', [App\Http\Controllers\LawyerController::class, 'verifyOtp'])->name('lawyer.otp.verify');
    Route::post('/logout', [App\Http\Controllers\LawyerController::class, 'logout'])->name('lawyer.logout');
    
    // Protected lawyer routes
    Route::middleware(['lawyer.auth'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\LawyerController::class, 'dashboard'])->name('lawyer.dashboard');
        Route::get('/complaint/{complaint}', [App\Http\Controllers\LawyerController::class, 'viewComplaint'])->name('lawyer.complaint.view');
        Route::post('/complaint/{complaint}/respond', [App\Http\Controllers\LawyerController::class, 'submitResponse'])->name('lawyer.complaint.respond');
        Route::get('/profile', [App\Http\Controllers\LawyerController::class, 'profile'])->name('lawyer.profile');
        Route::put('/profile', [App\Http\Controllers\LawyerController::class, 'updateProfile'])->name('lawyer.profile.update');
    });
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/track', [PublicTrackingController::class, 'show'])->name('login.guest');
Route::get('/login/cast-member', [LoginController::class, 'showCastMemberLoginForm'])->name('login.cast-member');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Guest routes (accessible by all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');
});

// Cast Member routes
// Route::middleware(['auth', 'is_cast_member'])->group(function () {
//     Route::get('/responses/create/{complaint}', [ResponseController::class, 'create'])->name('responses.create');
//     Route::post('/responses/{complaint}', [ResponseController::class, 'store'])->name('responses.store');
// });

// Admin Routes
Route::middleware(['auth', 'is_admin'])->group(function () {
    // Dashboard
    Route::get('admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Complaints Management
    Route::get('admin/complaints', [AdminComplaintController::class, 'index'])->name('admin.complaints');
    Route::post('/admin/complaints/{complaint}/preview-resolution', [ComplaintResolutionController::class, 'preview'])->name('admin.complaints.preview-resolution');
    Route::get('/admin/complaints/{complaint}/signature-status', [ComplaintResolutionController::class, 'getSignatureStatus'])->name('admin.complaints.signature-status');
    Route::get('admin/complaints/create', [AdminComplaintController::class, 'create'])->name('admin.complaints.create');
    Route::post('admin/complaints', [AdminComplaintController::class, 'store'])->name('admin.complaints.store');
    // Specific complaint routes (must come before general {complaint} routes)
    Route::get('admin/complaints/{complaint}/email-preview', [AdminComplaintController::class, 'emailPreview'])->name('admin.complaints.email-preview');
    Route::get('admin/complaints/{complaint}/edit', [AdminComplaintController::class, 'edit'])->name('admin.complaints.edit');
    Route::patch('admin/complaints/{complaint}/update-status', [AdminComplaintController::class, 'updateStatus'])->name('admin.complaints.update-status');
    Route::patch('admin/complaints/{complaint}/update-stage', [AdminComplaintController::class, 'updateStage'])->name('admin.complaints.update-stage');
    Route::post('admin/complaints/{complaint}/add-respondent', [AdminComplaintController::class, 'addRespondentToComplaint'])->name('admin.complaints.add-respondent');
    Route::post('admin/complaints/{complaint}/add-lawyer', [AdminComplaintController::class, 'addLawyerToComplaint'])->name('admin.complaints.add-lawyer');
    Route::post('admin/complaints/{complaint}/send-to', [AdminComplaintController::class, 'sendTo'])->name('admin.complaints.send-to');
    Route::post('admin/complaints/{complaint}/reply', [AdminComplaintController::class, 'storeReply'])->name('admin.complaints.reply');

    // General complaint routes
    Route::get('admin/complaints/{complaint}', [AdminComplaintController::class, 'show'])->name('admin.complaints.show');
    Route::put('admin/complaints/{complaint}', [AdminComplaintController::class, 'update'])->name('admin.complaints.update');
    Route::delete('admin/complaints/{complaint}', [AdminComplaintController::class, 'destroy'])->name('admin.complaints.destroy');

    // Attachments
    Route::get('admin/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('admin.attachments.delete');

    // Respondents
    Route::post('admin/complaints/respondent', [AdminComplaintController::class, 'addRespondent'])->name('admin.complaints.respondent');

    // Stages Management
    Route::get('admin/stages', [\App\Http\Controllers\Admin\StageController::class, 'index'])->name('admin.stages.index');
    Route::post('admin/stages', [\App\Http\Controllers\Admin\StageController::class, 'store'])->name('admin.stages.store');
    Route::put('admin/stages/{stage}', [\App\Http\Controllers\Admin\StageController::class, 'update'])->name('admin.stages.update');
    Route::delete('admin/stages/{stage}', [\App\Http\Controllers\Admin\StageController::class, 'destroy'])->name('admin.stages.destroy');
    Route::patch('admin/stages/{stage}/toggle', [\App\Http\Controllers\Admin\StageController::class, 'toggle'])->name('admin.stages.toggle');
    Route::post('admin/complaints/investigate/{complaint}', [AdminComplaintController::class, 'investigate'])->name('admin.complaints.investigate');
    Route::get('admin/complaints/{complaint}/investigation-history', [AdminComplaintController::class, 'investigationHistory'])->name('admin.complaints.investigation-history');
    Route::get('admin/complaints/{complaint}/investigation/{log}', [AdminComplaintController::class, 'showInvestigationLog'])->name('admin.complaints.investigation.show');
    Route::put('admin/complaints/{complaint}/investigation/{log}', [AdminComplaintController::class, 'updateInvestigationLog'])->name('admin.complaints.investigation.update');
    Route::delete('admin/complaints/{complaint}/investigation/{log}', [AdminComplaintController::class, 'deleteInvestigationLog'])->name('admin.complaints.investigation.delete');

    // Response Management Routes
    Route::post('admin/responses/{response}', [AdminComplaintController::class, 'updateResponse'])->name('admin.responses.update');
    Route::delete('admin/responses/{response}', [AdminComplaintController::class, 'deleteResponse'])->name('admin.responses.delete');
});
// Cast Member Routes
Route::middleware(['auth', 'cast.member'])->prefix('cast-member')->name('cast_member.')->group(function () {
    Route::get('/dashboard', [CastMemberController::class, 'dashboard'])->name('dashboard');
    Route::get('/complaints', [CastMemberController::class, 'complaints'])->name('complaints');
    Route::get('/complaints/{complaint}', [CastMemberController::class, 'showComplaint'])->name('complaints.show');
    Route::post('/complaints/{complaint}/respond', [CastMemberController::class, 'respond'])->name('complaints.respond');
    Route::get('/attachments/{attachment}/download', [CastMemberController::class, 'downloadAttachment'])->name('attachments.download');
    Route::get('/documents', [CastMemberController::class, 'documents'])->name('documents');
    Route::get('/settings', [CastMemberController::class, 'settings'])->name('settings');
});

// Admin routes
Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/admin/complaints', [AdminComplaintController::class, 'index'])->name('admin.complaints');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::get('/admin/complaints/{complaint}/investigate', [AdminController::class, 'investigate'])->name('admin.investigate');
    Route::post('/admin/complaints/{complaint}/resolve', [AdminController::class, 'resolve'])->name('admin.resolve');

    // Complaint Respondent Management
    Route::post('/admin/complaints/{complaint}/respondents', [\App\Http\Controllers\Admin\ComplaintRespondentController::class, 'assign'])->name('admin.complaints.respondents.assign');
    Route::delete('/admin/complaints/{complaint}/respondents/{respondent}', [\App\Http\Controllers\Admin\ComplaintRespondentController::class, 'remove'])->name('admin.complaints.respondents.remove');

    // Complaint Lawyer Management
    Route::post('/admin/complaints/{complaint}/lawyers', [\App\Http\Controllers\Admin\ComplaintLawyerController::class, 'assign'])->name('admin.complaints.lawyers.assign');
    Route::delete('/admin/complaints/{complaint}/lawyers/{lawyer}', [\App\Http\Controllers\Admin\ComplaintLawyerController::class, 'remove'])->name('admin.complaints.lawyers.remove');
    // User Management Routes
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');

    // Location Management Routes
    Route::get('/admin/locations', [\App\Http\Controllers\Admin\LocationController::class, 'index'])->name('admin.locations');
    Route::post('/admin/locations', [\App\Http\Controllers\Admin\LocationController::class, 'store'])->name('admin.locations.store');
    Route::put('/admin/locations/{location}', [\App\Http\Controllers\Admin\LocationController::class, 'update'])->name('admin.locations.update');
    Route::delete('/admin/locations/{location}', [\App\Http\Controllers\Admin\LocationController::class, 'destroy'])->name('admin.locations.destroy');

    // Documents and Reports Routes
    Route::get('/admin/documents', [AdminController::class, 'documents'])->name('admin.documents');
    Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
});

// Complaint routes
Route::middleware(['auth'])->group(function () {
    Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');
    Route::get('/complaints/{complaint}/edit', [ComplaintController::class, 'edit'])->name('complaints.edit');
    Route::post('/complaints/{complaint}/update-status', [AdminComplaintController::class, 'updateStatus'])->name('complaints.update-status');
    Route::post('/complaints/{complaint}/preview-resolution', [ComplaintResolutionController::class, 'preview'])->name('complaints.preview-resolution');
    Route::post('/resolution/upload-signature', [ComplaintResolutionController::class, 'uploadSignature'])->name('resolution.upload-signature');
    Route::post('/complaints/{complaint}/generate-resolution', [ComplaintResolutionController::class, 'generate'])->name('admin.complaints.generate-resolution');
    Route::put('/complaints/{complaint}', [ComplaintController::class, 'update'])->name('complaints.update');
    Route::delete('/complaints/{complaint}', [ComplaintController::class, 'destroy'])->name('complaints.destroy');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
