<?php

// logowanie do panelu administracyjnego (ponowne wpisanie hasla)
/** @var $this \Illuminate\Routing\Router */
$this->match(['get', 'post'], 'Adm', [
    'uses' => 'Adm\HomeController@index',
    'as' => 'adm.home',
    'middleware' => [
        'auth', 'can:adm-access', 'adm:0'
    ]
]);

// dostep do panelu administracyjnego
$this->group(
    ['namespace' => 'Adm', 'middleware' => ['auth', 'can:adm-access', 'adm:1'], 'prefix' => 'Adm', 'as' => 'adm.'],
    function () {
        /** @var $this \Illuminate\Routing\Router */
        $this->get('Dashboard', 'DashboardController@index')->name('dashboard');

        $this->get('Forum/Categories', 'Forum\CategoriesController@index')->name('forum.categories');

        $this->get('Forum/Categories/Save/{id?}', [
            'uses' => 'Forum\CategoriesController@edit',
            'as' => 'forum.categories.save'
        ]);

        $this->post('Forum/Categories/Save/{id?}', ['uses' => 'Forum\CategoriesController@save']);

        $this->get('Forum/Permissions', [
            'as' => 'forum.permissions',
            'uses' => 'Forum\PermissionsController@index',
            'middleware' => 'can:adm-group'
        ]);

        $this->get('Forum/Permissions/Save', [
            'as' => 'forum.permissions.save',
            'uses' => 'Forum\PermissionsController@edit',
            'middleware' => 'can:adm-group'
        ]);

        $this->post('Forum/Permissions/Save', [
            'uses' => 'Forum\PermissionsController@save',
            'middleware' => 'can:adm-group'
        ]);

        /*
         * Forum reasons
         */

        $this->get('Forum/Reasons', 'Forum\ReasonsController@index')->name('forum.reasons');

        $this->get('Forum/Reasons/Save/{id?}', [
            'uses' => 'Forum\ReasonsController@edit',
            'as' => 'forum.reasons.save'
        ]);

        $this->post('Forum/Reasons/Save/{id?}', ['uses' => 'Forum\ReasonsController@save']);
        $this->post('Forum/Reasons/Delete/{id}', [
            'as' => 'forum.reasons.delete',
            'uses' => 'Forum\ReasonsController@delete'
        ]);

        ////////////////////////////////////////////////////////////////////////////////////


        $this->get('User', 'UserController@index')->name('user');
        $this->get('User/Save/{user}', 'UserController@edit')->name('user.save');
        $this->post('User/Save/{user}', 'UserController@save');

        $this->get('Firewall', 'FirewallController@index')->name('firewall');
        $this->get('Firewall/Save/{firewall?}', 'FirewallController@edit')->name('firewall.save');
        $this->post('Firewall/Save/{firewall?}', 'FirewallController@save');
        $this->post('Firewall/Delete/{firewall}', 'FirewallController@delete')->name('firewall.delete');

        $this->get('Stream', 'StreamController@index')->name('stream');
        $this->get('Cache', 'CacheController@index')->name('cache');

        $this->get('Stream', 'StreamController@index')->name('stream');

        $this->get('Flag', 'FlagController@index')->name('flag');
        $this->get('Log', 'LogController@index')->name('log');

        $this->get('Groups', ['uses' => 'GroupsController@index', 'middleware' => 'can:adm-group'])->name('groups');

        $this->get('Groups/Save/{group?}', [
            'uses' => 'GroupsController@edit',
            'middleware' => 'can:adm-group',
            'as' => 'groups.save'
        ]);

        $this->post('Groups/Save/{group?}', [
            'uses' => 'GroupsController@save',
            'middleware' => 'can:adm-group',
        ]);

        $this->post('Groups/Delete/{group}', [
            'uses' => 'GroupsController@delete',
            'middleware' => 'can:adm-group',
            'as' => 'groups.delete'
        ]);

        $this->get('Sessions', ['uses' => 'SessionsController@index'])->name('sessions');

        $this->get('Words', ['uses' => 'WordsController@index'])->name('words');
        $this->post('Words', ['uses' => 'WordsController@save'])->name('words.save');

        $this->get('Block', ['uses' => 'BlocksController@index'])->name('blocks');
        $this->get('Blocks/Save/{block?}', 'BlocksController@edit')->name('blocks.save');
        $this->post('Blocks/Save/{block?}', 'BlocksController@save');
        $this->post('Blocks/Delete/{block}', 'BlocksController@delete')->name('blocks.delete');
    }
);
