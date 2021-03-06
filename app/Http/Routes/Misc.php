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

/** @var $this \Illuminate\Routing\Router */
$this->get('/', ['as' => 'home', 'uses' => 'HomeController@index', 'middleware' => 'cache:15']);

$this->get('Flag', ['uses' => 'FlagController@index', 'as' => 'flag', 'middleware' => 'auth']);
$this->post('Flag', ['uses' => 'FlagController@save', 'middleware' => 'auth']);
$this->post('Flag/Delete/{id}', ['uses' => 'FlagController@delete', 'middleware' => 'auth', 'as' => 'flag.delete']);
$this->get('Flag/Delete/{id}', ['uses' => 'FlagController@modal', 'middleware' => 'auth', 'as' => 'flag.modal']);

$this->get('sitemap/{sitemap?}', ['uses' => 'SitemapController@index', 'as' => 'sitemap']);

$this->get('Search', ['uses' => 'SearchController@index', 'as' => 'search']);
