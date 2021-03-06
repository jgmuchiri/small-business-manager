<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::group(['middleware' => ['web']], function () {

    //guest routes
    Route::post('contact', 'HomeController@sendMessage');
    Route::get('/', 'HomeController@index');

    Route::get('dashboard', 'Homecontroller@dashboard');
    Route::get('logout', 'Auth\AuthController@getLogout');

    Route::get('register/verify/{confirmationCode}', [
        'as' => 'confirmation_path',
        'uses' => 'Auth\AuthController@confirmAccount',
    ]);
    //    Route::get('register', 'Auth\AuthController@showRegistrationForm');
    //    Route::post('register', 'Auth\AuthController@createUser');
    //    Route::get('register/confirm', 'Auth\AuthController@resendConfirmation');

    //settings
    Route::group([
        'prefix' => 'admin',
        'middleware' => ['auth', 'role:admin'],
        'namespace' => 'Admin',
    ], function () {

        Route::group(['prefix' => 'settings'], function () {
            Route::get('/', 'AdminController@settings')->name('view-settings');
            Route::post('/', 'AdminController@updateEnv')->name('update-env');
            Route::post('backup', 'AdminController@backupEnv')->name("backup-env");
        });

        Route::post('/logo', 'AdminController@uploadLogo')->name('update-logo');

        Route::resource('roles', 'RoleController')->except('show');
    });

    //users
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'UserController@users');
        Route::get('{id}/view', 'UserController@user');
        Route::get('findUser', 'UserController@findUser');
        Route::post('export', 'UserController@export');
        Route::post('register', 'UserController@registerUser');
        Route::post('{id}', 'UserController@updateUser');

        Route::post('{id}/update-role', 'UserController@updateUserRole');
    });

    //routes for all
    Route::group(['prefix' => 'account'], function () {
        Route::get('/', function () {
            return view('account.dashboard');
        });
        Route::get('profile', 'UserController@profile');
        Route::post('profile', 'UserController@updateProfile');
    });

    //billing - invoices
    Route::group(['prefix' => 'invoice'], function () {
        Route::get('/', 'InvoiceController@index');
        Route::get('create', 'InvoiceController@create');
        Route::post('create', 'InvoiceController@storeInvoice');
        Route::post('{id}/update', 'InvoiceController@updateInvoice');

        Route::get('{id}/edit', 'InvoiceController@editInvoice');
        Route::get('{id}/replicate', 'InvoiceController@replicateInvoice');
        Route::get('{invoice}/removeItem/{id}', 'InvoiceController@InvoiceRemoveItem');
        Route::post('{id}/delete', 'InvoiceController@deleteInvoice');

        Route::get('inventory', 'InvoiceController@inventory');
        Route::post('addInventoryItem', 'InvoiceController@addInventoryItem');
        Route::get('delete-inventory/{id}', 'InvoiceController@deleteInventoryItem');
        Route::get('inventoryJson', 'InvoiceController@inventoryJson');

        Route::get('{id}/view', 'InvoiceController@viewInvoice');
        Route::get('{id}/pay/{user}', 'InvoiceController@payInvoice');

        Route::post('payment', 'InvoiceController@manualPay');
        Route::post('stripe-pay', 'InvoiceController@stripePay');
        Route::post('send-to-email', 'InvoiceController@sendToEmail');
        Route::get('{id}/email-reminder', 'InvoiceController@sendReminder');

        Route::get('/client/{id}', 'InvoiceController@client');
    });

    //billing - expenses
    Route::group(['prefix' => 'expenses', 'is' => 'admin|manager'], function () {
        Route::get('/', 'ExpensesController@index');
        Route::get('{id}/edit', 'ExpensesController@index');
        Route::post('/', 'ExpensesController@store');
        Route::post('/newCat', 'ExpensesController@addCategory');
        Route::post('{id}/update', 'ExpensesController@update');
        Route::get('{id}/delete', 'ExpensesController@destroy');
    });

    //billing -income
    Route::group(['prefix' => 'income', 'is' => 'admin|manager'], function () {
        Route::get('/', 'IncomeController@index');
    });

    //billing -checks
    Route::group(['prefix' => 'checks'], function () {
        Route::get('/', 'ChecksController@index');
        Route::post('/', 'ChecksController@store');
        Route::get('{id}/view', 'ChecksController@view');
        Route::get('{id}/status/{status}', 'ChecksController@updateStatus');
        Route::get('{id}/delete', 'ChecksController@deleteCheck');
    });

    Route::post('checkout', 'TransactionsController@checkout');

    Route::post('membership/support/checkout', 'TransactionsController@supportSubscribe');

    //contacts
    Route::resource('contacts', 'ContactsController')->middleware('auth');

    Route::group(['prefix' => 'contacts'], function () {
        Route::get('group/{id}/view', 'ContactsController@viewByGroup');
        Route::post('/groups', 'ContactsController@createGroup');
        Route::get('/groups/{id}/edit', 'ContactsController@editGroup');
        Route::get('/groups/viewAjax', 'ContactsController@ajaxViewGroups');
        Route::post('/groups/{id}/update', 'ContactsController@updateGroup');
        Route::get('/groups/{id}/delete', 'ContactsController@destroyGroup');
    });

    //projects
    Route::group(['prefix' => 'projects'], function () {
        Route::get('/', 'ProjectsController@index');
        Route::post('/', 'ProjectsController@createProject');
        Route::get('/{id}/edit', 'ProjectsController@editProject');
        Route::post('/{id}/update', 'ProjectsController@updateProject');
        Route::get('/{id}/delete', 'ProjectsController@deleteProject');

        Route::get('/{id}/view', 'ProjectsController@view');
        Route::get('/{id}/milestones', 'ProjectsController@milestones');
        Route::get('/{id}/tasks', 'ProjectsController@tasks');
        Route::get('/{id}/files', 'ProjectsController@files');
        Route::get('/{id}/messages', 'ProjectsController@messages');
        Route::get('/{id}/members', 'ProjectsController@members');

        Route::get('/{id}/milestone/{mid}/view', 'ProjectsController@milestones');
        Route::post('/{id}/create-milestone', 'ProjectsController@createMilestone');
        Route::get('/edit-milestone/{id}', 'ProjectsController@editMilestone');
        Route::post('/update-milestone/{id}', 'ProjectsController@updateMilestone');
        Route::get('/delete-milestone/{id}', 'ProjectsController@deleteMilestone');

        Route::get('/{id}/milestone/{mid}/tasks', 'ProjectsController@tasks');
        Route::post('create-task', 'ProjectsController@createTask');
        Route::get('/edit-task/{id}', 'ProjectsController@editTask');
        Route::post('update-task-status', 'ProjectsController@updateTaskStatus');
        Route::post('update-task', 'ProjectsController@updateTask');
        Route::get('pay-task/{id}', 'ProjectsController@payTask');
        Route::get('delete-task/{id}', 'ProjectsController@deleteTask');

        Route::post('/file/create', 'ProjectsController@createFile');
        Route::get('/file/{id}/delete', 'ProjectsController@deleteFile');

        Route::post('create-message', 'ProjectsController@createMessage');
        Route::post('/reply-message/{id}', 'ProjectsController@replyMessage');
        Route::get('/delete-message/{id}', 'ProjectsController@deleteMessage');

        Route::post('upload-file', 'ProjectsController@uploadFile');
        Route::get('file', 'ProjectsController@downloadFile');
        Route::get('delete-file', 'ProjectsController@deleteFile');

        Route::post('/members/create', 'ProjectsController@addMember');
        Route::get('/members/{id}/remove', 'ProjectsController@removeMember');
    });
    Route::get('logs', 'LogsController@index');
});
