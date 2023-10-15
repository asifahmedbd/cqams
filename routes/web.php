<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;

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
});
Route::get('phpmyinfo', function () {
    phpinfo(); 
})->name('phpmyinfo');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); 
});

Route::middleware(['auth', 'checkrole:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/logout', [AdminDashboardController::class, 'adminlogout'])->name('admin.logout');
    Route::resource('/admin/category', 'CategoryController');
    Route::resource('/admin/attributes', 'AttributesController');
    Route::resource('/admin/products', 'ProductController');
    Route::post('/get-product-details', 'ProductController@getProductDetails')->name('get-product-details');

    //Institution
    Route::get('/admin/addInstitution', 'InstituteController@addInstitutionForm')->name('add-institute');
    Route::get('/admin/institutionList', 'InstituteController@institutionList')->name('show-institutes');
    Route::post('/admin/insitution/save', 'InstituteController@institutionSave');
    Route::get('/admin/edit/{id}/institute', 'InstituteController@institutionEdit');
    Route::get('/getDistrict/{division}', 'InstituteController@getDistrict');
    Route::get('/getUpazila/{district}', 'InstituteController@getUpazila');

    //Scale Settings
    Route::get('/admin/scale', 'ScaleController@scale')->name('scale-list');
    Route::post('/admin/storeScale', 'ScaleController@storeScale');
    Route::get('/admin/delete/scale/{id}', 'ScaleController@deleteScale');

    Route::get('/admin/scale-set', 'ScaleController@scaleSetList')->name('scale-set');
    Route::post('/admin/storeScaleSet', 'ScaleController@storeScaleSet');
    Route::get('/admin/delete/scale-set/{id}', 'ScaleController@deleteScaleSet');

    // TQAMS questions category
    Route::get('/admin/tqams/add-new-criteria', 'TqamsController@addMultiplecriteria')->name('question-category');
    Route::post('/admin/tqams/storeCriteriaMultiple', 'TqamsController@storeCriteriaMultiple');

    Route::get('/admin/tqams/add-new-sub-criteria', 'TqamsController@addMultipleSubcriteria')->name('question-subcategory');

    Route::get('/admin/tqams/add-new-question', 'TqamsController@addMultipleQuestion')->name('tqams-questions');
    Route::get('/get-tqams-criteria/{id}', 'TqamsController@getSubCriteria');
    Route::get('/getTqamsQuestionform/{number}', 'TqamsController@getQuestionField');
    Route::post('/admin/tqams/storeFieldsMultiple', 'TqamsController@storeFieldsmultiple');
    Route::get('/get-question-details/{id}', 'TqamsController@getQuestionDetails');
    Route::post('/admin/tqams/question/update', 'TqamsController@updateQuestion');

    // Session Management
    Route::get('/admin/manage-session', 'AcademicSessionController@index')->name('manage-session');
    Route::post('/admin/storeAcademicSession', 'AcademicSessionController@storeAcademicSession');

    // School-wise Questions evaluation settings
    Route::get('/admin/schoolwise-evaluations', 'TqamsController@schoolwiseEvaluations')->name('schoolwise-evaluations');
    Route::get('/get-school-types/{school_id}', 'TqamsController@getSchoolTypes');
    Route::post('/admin/addEvaluation', 'TqamsController@storeSchoolwiseEvaluation');
    Route::get('/admin/submitEvaluation/{eval_id}', 'TqamsController@submitEvaluation')->name('submit-evaluation');
    Route::post('/admin/tqams/submit', 'TqamsController@submit');

    //Reports routes
    Route::get('/admin/tqams_report', 'AcademicReportsController@showTqamsReport')->name('tqams-reports');
    Route::get('/get-evaluation-data/{session_id}/{school_id}', 'AcademicReportsController@getEvaluationData');
    Route::get('/admin/tqams-report-ajax/{academic_session}/{school_id}/{eval_id}/{generate_pdf}', 'AcademicReportsController@getTqamsReportBySession');


});



require __DIR__.'/auth.php';
