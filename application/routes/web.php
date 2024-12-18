<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function default_resource($name, $controller=null, $middlewares=[], $extend_resource=null)
{
    $controller = $controller ? $controller : str_replace(' ', '', ucwords(str_replace(['-'], ' ', $name))).'Controller';

    $group = str_replace('-', '/', $name);

    Route::group($group, ['middleware' => $middlewares], function () use ($name, $controller, $extend_resource)
    {
        Route::get('/', $controller.'@index', ['middleware' => ['SmartyMiddleware']])->name($name);

        Route::get('new', $controller.'@new', ['middleware' => ['SmartyMiddleware']])->name($name.'.new');

        Route::post('/', $controller.'@create')->name($name.'.create');

        Route::get('{num:id}', $controller.'@edit', ['middleware' => ['SmartyMiddleware']])->name($name.'.edit');

        Route::post('{num:id}', $controller.'@update')->name($name.'.update');

        Route::delete('{num:id}', $controller.'@delete')->name($name.'.delete');

        if ($extend_resource) $extend_resource($name, $controller);
    });
}

Route::get('pwd', function ()
{
    json_response(true, ['password' => encryptPassword(get('pwd') ?? '12345')]);
});

Route::set('404_override', function()
{
    show_404();
});

Route::set('translate_uri_dashes', FALSE);

Route::group('/', function ()
{
    $name = 'login';
    $controller = 'LoginController';

    Route::group('/', ['middleware' => ['SessionMiddleware']], function () use ($name, $controller)
    {
        Route::get('login', $controller.'@login', ['middleware' => ['SmartyMiddleware']])->name($name);

        Route::post('login', $controller.'@login_check')->name($name.'.check');

        Route::get('logout', $controller.'@logout')->name('logout');
    });
});

Route::group('register', function ()
{
    $name = 'register';
    $controller = 'RegisterController';

    Route::group('/', ['middleware' => ['SessionMiddleware']], function () use ($name, $controller)
    {
        Route::get('/', $controller.'@new', ['middleware' => ['SmartyMiddleware']])->name($name);

        Route::post('/', $controller.'@create')->name($name.'.create');
    });
});

Route::group('/', ['middleware' => ['SessionMiddleware', 'CheckLoginMiddleware', 'StudentFormMiddleware']], function()
{
    Route::group('/', function ()
    {
        $name = 'home';
        $controller = 'HomeController';

        Route::get('/', $controller.'@home', ['middleware' => ['SmartyMiddleware']])->name($name);

        Route::get('about-us', $controller.'@about_us', ['middleware' => ['SmartyMiddleware']])->name($name.'.about-us');
    });

    Route::group('profile', function ()
    {
        $name = 'profile';
        $controller = 'ProfileController';

        Route::get('/', $controller.'@index', ['middleware' => ['SmartyMiddleware']])->name($name);

        Route::post('/', $controller.'@update_user_profile')->name($name.'.update-user');
        
        Route::post('password', $controller.'@password')->name($name.'.password');

        Route::post('doctor', $controller.'@save_doctor')->name($name.'.save-doctor');
    });

    default_resource('admins', null, ['AdminMiddleware']);

    # default_resource('specialties', null, ['StaffMiddleware']);

    default_resource('announcements', null, ['StaffMiddleware']);
    
    default_resource('transaction-types', null, ['StaffMiddleware']);

    default_resource('appointment-limits', null, ['StaffMiddleware'], function ($name, $controller)
    {
        Route::get('date/limits', $controller.'@date_limits', ['middleware' => ['SmartyMiddleware']])->name($name.'.date-limits');
    });

    default_resource('doctors', null, ['StaffMiddleware']);

    default_resource('nurses', null, ['StaffMiddleware']);

    default_resource('patients', null, ['StaffMiddleware'], function ($name, $controller)
    {
        Route::get('{num:id}/records', $controller.'@records', ['middleware' => ['SmartyMiddleware']])->name($name.'.records');
    });

    Route::group('patients/{num:patient_id}/transactions', ['middleware' => []], function ()
    {
        $name = 'patient-transaction';

        $controller = 'PatientTransactionsController';

        Route::get('/', $controller.'@index', ['middleware' => []])->name($name);

        Route::get('new', $controller.'@new', ['middleware' => ['SmartyMiddleware']])->name($name.'.new');

        Route::post('/', $controller.'@create')->name($name.'.create');

        Route::get('{num:id}', $controller.'@edit', ['middleware' => ['SmartyMiddleware']])->name($name.'.edit');

        Route::post('{num:id}', $controller.'@update')->name($name.'.update');

        Route::delete('{num:id}', $controller.'@delete')->name($name.'.delete');
    });

    default_resource('users', null, ['AdminMiddleware']);

    default_resource('users-appointments', null, [], function ($name, $controller)
    {
        Route::get('{num:id}/cancel', $controller.'@cancel_form', ['middleware' => ['SmartyMiddleware']])->name($name.'.cancel-form');

        Route::post('{num:id}/cancel', $controller.'@cancel')->name($name.'.cancel');

        Route::get('doctors', $controller.'@specialty_doctors', ['middleware' => ['SmartyMiddleware']])->name($name.'.doctors');
    });

    Route::group('users/transactions', ['middleware' => ['UserMiddleware']], function ()
    {
        $name = 'users-transactions';
        $controller = 'UsersTransactionsController';

        Route::get('/', $controller.'@index', ['middleware' => ['SmartyMiddleware']])->name($name);
    });

    default_resource('appointments', null, ['StaffMiddleware']);

    Route::group('transactions', ['middleware' => ['StaffMiddleware']], function ()
    {
        $name = 'transactions';
        $controller = 'TransactionsController';

        Route::get('/', $controller.'@index', ['middleware' => ['SmartyMiddleware']])->name($name);
    });

    Route::group('message', ['middleware' => []], function ()
    {
        $name = 'messages';
        $controller = 'MessagesController';

        Route::get('/user', $controller.'@user', ['middleware' => ['UserMiddleware', 'SmartyMiddleware']])->name($name.'.user');

        Route::post('/user', $controller.'@user_message', ['middleware' => ['UserMiddleware']])->name($name.'.user-message');

        Route::get('/staff', $controller.'@staff', ['middleware' => ['StaffMiddleware', 'SmartyMiddleware']])->name($name.'.staff');

        Route::get('/user/{num:user_id}', $controller.'@staff_user', ['middleware' => ['StaffMiddleware', 'SmartyMiddleware']])->name($name.'.staff-user');

        Route::post('/user/{num:user_id}', $controller.'@staff_user_message', ['middleware' => ['StaffMiddleware']])->name($name.'.staff-user-message');
    });
});