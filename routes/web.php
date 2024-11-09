<?php

use App\Http\Controllers\Approval\ApprovalController;
use App\Http\Controllers\ExternalController;
use App\Http\Controllers\Formula\SemiFinishController;
use App\Http\Controllers\Keycode\KeyCodeController;
use App\Http\Controllers\Master\DivisionController;
use App\Http\Controllers\Master\ProductCIController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Master\SubDivisionController;
use App\Http\Controllers\MaterialSAPController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Role\NeedOtpController;
use App\Http\Controllers\Role\PermissionController;
use App\Http\Controllers\Scaleup\DraftScaleupController;
use App\Http\Controllers\Scaleup\DuplicateScaleupController;
use App\Http\Controllers\Scaleup\EditScaleupController;
// use App\Http\Controllers\Scaleup\PrintController;
use App\Http\Controllers\Scaleup\ScaleUpController;
use App\Http\Controllers\User\UserController;
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


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/external/{param}', [ExternalController::class, 'scaleup'])->name('external.scaleup');
Route::post('/external/{param}', [ExternalController::class, 'scaleupStore'])->name('external.scaleup.store');
Route::get('/external/keycode/{param}', [ExternalController::class, 'keycode'])->name('external.keycode');
Route::post('/external/keycode/{param}', [ExternalController::class, 'keyCodeStore'])->name('external.keycode.store');
Route::middleware('auth')->group(function () {

    Route::get('/', [NeedOtpController::class, 'index'])->name('index');
    // Route::get('/choose', [NeedOtpController::class, 'choose'])->name('choose')->withoutMiddleware('cek.otp');
    // Route::post('/choose', [NeedOtpController::class, 'requestMenu'])->name('requestMenu')->withoutMiddleware('cek.otp');
    // Route::post('/verifikasiOtp', [NeedOtpController::class, 'verifikasiOtp'])->name('verifikasiOtp')->withoutMiddleware('cek.otp');

    // Keycode
    Route::get('/keycode/pending', [KeyCodeController::class, 'pending'])->name('keycode.pending');
    Route::get('/keycode/complete', [KeyCodeController::class, 'complete'])->name('keycode.complete');

    Route::get('/menu', [KeyCodeController::class, 'menu'])->name('keycode.menu');
    Route::get('/input', [KeyCodeController::class, 'input'])->name('keycode.input');
    Route::post('/input', [KeyCodeController::class, 'process'])->name('keycode.process');
    Route::post('/menu', [KeyCodeController::class, 'request'])->name('keycode.request');
    Route::get('/menu/approve/{id}', [KeyCodeController::class, 'approve'])->name('keycode.approve');
    Route::post('/menu/approve/{id}', [KeyCodeController::class, 'approveStore'])->name('keycode.approveStore');
    Route::post('getApproval', [KeyCodeController::class, 'getApproval'])->name('keycode.getApproval');

    Route::get('/getMaterial', [ScaleUpController::class, 'getMaterial'])->name('api.getMaterial');
    Route::get('/getMaterialById', [ScaleUpController::class, 'getMaterialById'])->name('api.getMaterialById');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // master CI
    Route::get('/getProducts', [ProductCIController::class, 'getProducts'])->name('getProducts');
    Route::get('/productList/{type}', [ProductCIController::class, 'productList'])->name('productList');
    Route::get('/ci/create', [ProductCIController::class, 'create'])->name('ci.create');
    Route::get('/ci/edit/{id}', [ProductCIController::class, 'edit'])->name('ci.edit');
    Route::put('/ci/edit/{id}', [ProductCIController::class, 'update'])->name('ci.update');
    Route::post('/ci', [ProductCIController::class, 'store'])->name('ci.store');
    Route::get('/ci/list', [ProductCIController::class, 'list'])->name('ci.list');
    Route::get('/ci/rm', [ProductCIController::class, 'rm'])->name('ci.rm');
    Route::get('/ci/productExist', [ProductCIController::class, 'productExist'])->name('ci.productExist');
    Route::get('/ci/productById', [ProductCIController::class, 'productById'])->name('ci.productById');


    // Product Code
    Route::get('/pc/create', [ProductController::class, 'create'])->name('pc.create');
    Route::post('/pc', [ProductController::class, 'store'])->name('pc.store');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // master SAP
    Route::get('sap/list/{type}', [MaterialSAPController::class, 'list'])->name('sap.list');
    Route::get('sap/byId', [MaterialSAPController::class, 'byId'])->name('sap.byId');
    Route::get('sap/create/rm', [MaterialSAPController::class, 'createRM'])->name('sap.createRM');
    Route::post('sap/create', [MaterialSAPController::class, 'store'])->name('sap.store');


    // user
    Route::get('/user/search', [UserController::class, 'search'])->name('users.search');
    Route::get('/change-password', [UserController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('password.update');

    // Scale Up
    Route::get('/scaleup', [ScaleUpController::class, 'index'])->name('scaleup.index')->middleware(['role_or_permission:admin|scaleup-create|scaleup-edit|scaleup-delete|scaleup-approve']);
    // Route::get('/scaleup/create', [ScaleUpController::class, 'create'])->name('scaleup.create')->middleware(['role_or_permission:admin|scaleup-create']);

    Route::get('/scaleup/create/{id}', [ScaleUpController::class, 'createWithKeycode'])->name('scaleup.createWithKeycode')->middleware(['role_or_permission:admin|scaleup-create']);
    Route::post('/scaleup/create/{id}', [ScaleUpController::class, 'storeWithKeycode'])->name('scaleup.storeWithKeycode')->middleware(['role_or_permission:admin|scaleup-create']);

    Route::get('/scaleup/approve/{id}', [ScaleUpController::class, 'approve'])->name('scaleup.approve');
    Route::post('/scaleup/approvestore/{id}', [ScaleUpController::class, 'approvestore'])->name('scaleup.approvestore');
    Route::get('/scaleup/print/{id}', [PDFController::class, 'scaleupWithKeyCode'])->name('scaleup.print');
    // Route::get('/scaleup/print/{id}', [PDFController::class, 'scaleupPDF'])->name('scaleup.print');
    Route::post('/scaleup/store', [ScaleUpController::class, 'store'])->name('scaleup.store');

    Route::get('/scaleup/duplicate/{id}', [DuplicateScaleupController::class, 'duplicate'])->name('scaleup.duplicate')->middleware(['role_or_permission:admin|scaleup-create|scaleup-edit']);;
    Route::post('/scaleup/duplicate/{id}', [DuplicateScaleupController::class, 'store'])->name('scaleup.duplicate.store')->middleware(['role_or_permission:admin|scaleup-create|scaleup-edit']);;



    Route::post('/scaleup/item', [ScaleUpController::class, 'saveItem'])->name('scaleup.saveItem')->middleware(['role_or_permission:admin|scaleup-create|scaleup-edit']);
    Route::post('/scaleup/getItemById', [ScaleUpController::class, 'getItemById'])->name('scaleup.getItemById')->middleware(['role_or_permission:admin|scaleup-create|scaleup-edit']);
    Route::post('/scaleup/total', [ScaleUpController::class, 'SaveTotal'])->name('scaleup.SaveTotal');
    Route::post('/scaleup/itemCategory', [ScaleUpController::class, 'saveitemCategory'])->name('scaleup.itemCategory');
    Route::post('/scaleup/item/delete', [ScaleUpController::class, 'deleteItem'])->name('scaleup.deleteItem');
    Route::get('/scaleup/itemCategory', [ScaleUpController::class, 'getItemcategory'])->name('scaleup.getItemcategory');
    Route::get('/scaleup/sessionGetSubCategory', [ScaleUpController::class, 'sessionGetSubCategory'])->name('scaleup.sessionGetSubCategory');
    Route::put('/scaleup/updateItemcategory', [ScaleUpController::class, 'updateItemcategory'])->name('scaleup.updateItemcategory');
    Route::delete('/scaleup/deleteItemcategory', [ScaleUpController::class, 'deleteItemcategory'])->name('scaleup.deleteItemcategory');
    // Route::get('/scaleup/compare', [ScaleUpController::class, 'compare'])->name('scaleup.compare')->middleware(['role_or_permission:admin|scaleup-compare']);
    Route::get('/scaleup/listScaleUp', [ScaleUpController::class, 'listScaleUp'])->name('scaleup.listScaleUp');
    Route::get('/scaleup/listToSubmit', [ScaleUpController::class, 'listToSubmit'])->name('scaleup.listToSubmit');
    // Route::get('/scaleup/listScaleUp', [ScaleUpController::class, 'listScaleUp'])->name('scaleup.listScaleUp')->middleware(['role_or_permission:admin|scaleup-compare|formula-create']);
    Route::post('/scaleup/getByID', [ScaleUpController::class, 'getByID'])->name('scaleup.getByID');
    Route::post('/scaleup/getHeader', [ScaleUpController::class, 'getHeader'])->name('scaleup.getHeader');

    Route::get('scaleup/flow', [ScaleUpController::class, 'flow'])->name('scaleup.flow');
    Route::get('scaleup/complete', [ScaleUpController::class, 'complete'])->name('scaleup.complete');
    Route::get('scaleup/submit', [ScaleUpController::class, 'submit'])->name('scaleup.submit');
    Route::post('scaleup/submit', [ScaleUpController::class, 'submitStore'])->name('scaleup.submitStore');
    // Route::get('scaleup/{id}', [ScaleUpController::class, 'show'])->name('scaleup.show');
    Route::get('scaleup/{id}', [ScaleUpController::class, 'showWithKeyCode'])->name('scaleup.show');

    Route::prefix('/scaleup/edit')->group(function () {
        Route::delete('deleteItemcategory', [EditScaleupController::class, 'deleteItemcategory'])->name('edit.scaleup.deleteItemcategory');
        Route::get('sessionGetSubCategory', [EditScaleupController::class, 'sessionGetSubCategory'])->name('edit.scaleup.sessionGetSubCategory');
        Route::post('itemCategory', [EditScaleupController::class, 'saveitemCategory'])->name('edit.scaleup.itemCategory');
        Route::put('updateItemcategory', [EditScaleupController::class, 'updateItemcategory'])->name('edit.scaleup.updateItemcategory');
        Route::post('item', [EditScaleupController::class, 'saveItem'])->name('edit.scaleup.saveItem');
        Route::post('getItemById', [EditScaleupController::class, 'getItemById'])->name('edit.scaleup.getItemById');
        Route::post('item/delete', [EditScaleupController::class, 'deleteItem'])->name('edit.scaleup.deleteItem');

        Route::get('{id}', [EditScaleupController::class, 'edit'])->name('scaleup.edit');
        Route::put('{id}', [EditScaleupController::class, 'update'])->name('edit.scaleup.update');
    });

    // Draft
    Route::prefix('draft/scaleup')->group(function () {
        Route::get('/', [DraftScaleupController::class, 'index'])->name('draft.scaleup.index');
        Route::get('sessionGetSubCategory', [DraftScaleupController::class, 'sessionGetSubCategory'])->name('draft.scaleup.sessionGetSubCategory'); //ok
        Route::post('itemCategory', [DraftScaleupController::class, 'saveitemCategory'])->name('draft.scaleup.itemCategory');
        Route::put('updateItemcategory', [DraftScaleupController::class, 'updateItemcategory'])->name('draft.scaleup.updateItemcategory');
        Route::post('item', [DraftScaleupController::class, 'saveItem'])->name('draft.scaleup.saveItem')->withoutMiddleware('cek.otp');
        Route::post('getItemById', [DraftScaleupController::class, 'getItemById'])->name('draft.scaleup.getItemById')->withoutMiddleware('cek.otp');
        Route::post('item/delete', [DraftScaleupController::class, 'deleteItem'])->name('draft.scaleup.deleteItem');
        Route::delete('deleteItemcategory', [DraftScaleupController::class, 'deleteItemcategory'])->name('draft.scaleup.deleteItemcategory'); //ok

        Route::get('{id}', [DraftScaleupController::class, 'show'])->name('draft.scaleup.show'); //ok
        Route::put('{id}', [DraftScaleupController::class, 'update'])->name('draft.scaleup.update'); //ok
    });

    // Approval
    Route::prefix('/approve')->group(function () {
        Route::get('scaleup', [ApprovalController::class, 'needApprove'])->name('list.approval.scaleup');
        Route::get('keycode', [KeyCodeController::class, 'needApprove'])->name('list.approval.keycode');
        Route::post('scaleup/getApproval', [ApprovalController::class, 'getApproval'])->name('scaleup.getApproval');
    });

    // formula
    Route::prefix('/formula/sfg')->group(function () {
        Route::get('/', [SemiFinishController::class, 'index'])->name('sf.index');
        Route::get('/show/{id}', [SemiFinishController::class, 'show'])->name('sf.show');
        Route::get('/create', [SemiFinishController::class, 'create'])->name('sf.create');
        Route::post('/create', [SemiFinishController::class, 'store'])->name('sf.store');
    });

    Route::middleware('role:admin')->prefix('master')->group(function () {
        // master user
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
        Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/user/edit/{id}', [UserController::class, 'update'])->name('user.update');
        Route::post('/user', [UserController::class, 'store'])->name('user.store');

        Route::get('/reset-password/{user}', [UserController::class, 'showResetPasswordForm'])->name('password.reset');
        Route::post('/reset-password/{user}', [UserController::class, 'resetPassword'])->name('password.reset.post');

        // Divisi
        Route::prefix('/division')->group(function () {
            Route::get('/', [DivisionController::class, 'index'])->name('division.index');
            Route::get('/create', [DivisionController::class, 'create'])->name('division.create');
            Route::post('/create', [DivisionController::class, 'store'])->name('division.store');
            Route::get('/edit/{id}', [DivisionController::class, 'edit'])->name('division.edit');
            Route::put('/edit/{id}', [DivisionController::class, 'update'])->name('division.update');
        });

        Route::prefix('/sub-div')->group(function () {
            Route::get('/', [SubDivisionController::class, 'index'])->name('sub-div.index');
            Route::get('/create', [SubDivisionController::class, 'create'])->name('sub-div.create');
            Route::post('/create', [SubDivisionController::class, 'store'])->name('sub-div.store');
            Route::get('/edit/{id}', [SubDivisionController::class, 'edit'])->name('sub-div.edit');
            Route::put('/edit/{id}', [SubDivisionController::class, 'update'])->name('sub-div.update');
        });
        //Master Approval
        Route::prefix('/approval')->group(function () {
            Route::get('/', [ApprovalController::class, 'index'])->name('master.approval.index');
            Route::get('/create', [ApprovalController::class, 'create'])->name('master.approval.create');
            Route::post('/', [ApprovalController::class, 'store'])->name('master.approval.store');
            Route::get('/edit/{id}', [ApprovalController::class, 'edit'])->name('master.approval.edit');
            Route::put('/edit/{id}', [ApprovalController::class, 'put'])->name('master.approval.put');
        });

        // permission

        Route::prefix('/permission')->group(function () {
            // Route::get('/', [PermissionController::class, 'index'])->name('permission.index');
            Route::get('/user', [PermissionController::class, 'addPermissionToUser'])->name('permission.addPermissionToUser');
            Route::get('/user/{user_id}', [PermissionController::class, 'userPermissionList'])->name('permission.userPermissionList');
            Route::put('/user', [PermissionController::class, 'updateUserPermission'])->name('permission.updateUserPermission');
        });
        Route::prefix('/role')->group(function () {
            // Route::get('/', [PermissionController::class, 'index'])->name('permission.index');
            Route::get('/user', [PermissionController::class, 'addRoleToUser'])->name('permission.addRoleToUser');
            Route::get('/user/{user_id}', [PermissionController::class, 'userRoleList'])->name('permission.userRoleList');
            Route::put('/user', [PermissionController::class, 'updateUserRole'])->name('permission.updateUserRole');
        });
    });
});

require __DIR__ . '/auth.php';
