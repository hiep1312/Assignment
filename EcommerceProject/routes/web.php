<?php

use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Users\UserCreate;
use App\Livewire\Admin\Users\UserEdit;
use App\Livewire\Admin\Users\UserIndex;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function() {
    /* Users */
    Route::prefix('users')->name('users.')->group(function(){
        Route::get('/', UserIndex::class)->name('index');
        Route::get('/create', UserCreate::class)->name('create');
        Route::get('/edit/{user}', UserEdit::class)->name('edit');
    });


});

Route::name('auth.')->group(function() {
    Route::prefix('admin')->name('admin.')->group(function() {
        Route::get('/login', Login::class);
    });

});

Route::prefix('template')->name('template.')->group(function() {
    Route::match(['get', 'post'], '/', fn() => view('admin.template.index'))->name('index');
    Route::view('/auth-forgot-password-basic', 'admin.template.forgot-password')->name('auth-forgot-password-basic');
    Route::view('/auth-login-basic', 'admin.template.login')->name('auth-login-basic');
    Route::view('/auth-register-basic', 'admin.template.register')->name('auth-register-basic');
    Route::view('/cards-basic', 'admin.template.cards-basic')->name('cards-basic');
    Route::view('/extended-ui-perfect-scrollbar', 'admin.template.extended-ui-perfect-scrollbar')->name('extended-ui-perfect-scrollbar');
    Route::view('/extended-ui-text-divider', 'admin.template.extended-ui-text-divider')->name('extended-ui-text-divider');
    Route::view('/form-layouts-horizontal', 'admin.template.form-layouts-horizontal')->name('form-layouts-horizontal');
    Route::view('/form-layouts-vertical', 'admin.template.form-layouts-vertical')->name('form-layouts-vertical');
    Route::view('/forms-basic-inputs', 'admin.template.forms-basic-inputs')->name('forms-basic-inputs');
    Route::view('/forms-input-groups', 'admin.template.forms-input-groups')->name('forms-input-groups');
    Route::view('/icons-boxicons', 'admin.template.icons-boxicons')->name('icons-boxicons');
    Route::view('/layouts-blank', 'admin.template.layouts-blank')->name('layouts-blank');
    Route::view('/layouts-container', 'admin.template.layouts-container')->name('layouts-container');
    Route::view('/layouts-fluid', 'admin.template.layouts-fluid')->name('layouts-fluid');
    Route::view('/layouts-without-menu', 'admin.template.layouts-without-menu')->name('layouts-without-menu');
    Route::view('/layouts-without-navbar', 'admin.template.layouts-without-navbar')->name('layouts-without-navbar');
    Route::view('/pages-account-settings-account', 'admin.template.pages-account-settings-account')->name('pages-account-settings-account');
    Route::view('/pages-account-settings-connections', 'admin.template.pages-account-settings-connections')->name('pages-account-settings-connections');
    Route::view('/pages-account-settings-notifications', 'admin.template.pages-account-settings-notifications')->name('pages-account-settings-notifications');
    Route::view('/pages-misc-error', 'admin.pages.404')->name('pages-misc-error');
    Route::view('/pages-misc-under-maintenance', 'admin.pages.maintenance')->name('pages-misc-under-maintenance');
    Route::view('/tables-basic', 'admin.template.tables-basic')->name('tables-basic');
    Route::view('/ui-accordion', 'admin.template.ui-accordion')->name('ui-accordion');
    Route::view('/ui-alerts', 'admin.template.ui-alerts')->name('ui-alerts');
    Route::view('/ui-badges', 'admin.template.ui-badges')->name('ui-badges');
    Route::view('/ui-buttons', 'admin.template.ui-buttons')->name('ui-buttons');
    Route::view('/ui-carousel', 'admin.template.ui-carousel')->name('ui-carousel');
    Route::view('/ui-collapse', 'admin.template.ui-collapse')->name('ui-collapse');
    Route::view('/ui-dropdowns', 'admin.template.ui-dropdowns')->name('ui-dropdowns');
    Route::view('/ui-footer', 'admin.template.ui-footer')->name('ui-footer');
    Route::view('/ui-list-groups', 'admin.template.ui-list-groups')->name('ui-list-groups');
    Route::view('/ui-modals', 'admin.template.ui-modals')->name('ui-modals');
    Route::view('/ui-navbar', 'admin.template.ui-navbar')->name('ui-navbar');
    Route::view('/ui-offcanvas', 'admin.template.ui-offcanvas')->name('ui-offcanvas');
    Route::view('/ui-pagination-breadcrumbs', 'admin.template.ui-pagination-breadcrumbs')->name('ui-pagination-breadcrumbs');
    Route::view('/ui-progress', 'admin.template.ui-progress')->name('ui-progress');
    Route::view('/ui-spinners', 'admin.template.ui-spinners')->name('ui-spinners');
    Route::view('/ui-tabs-pills', 'admin.template.ui-tabs-pills')->name('ui-tabs-pills');
    Route::view('/ui-toasts', 'admin.template.ui-toasts')->name('ui-toasts');
    Route::view('/ui-tooltips-popovers', 'admin.template.ui-tooltips-popovers')->name('ui-tooltips-popovers');
    Route::view('/ui-typography', 'admin.template.ui-typography')->name('ui-typography');
});
