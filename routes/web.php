<?php

use brain\Http\Controllers\HyperTargetingController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::get('/user_profile', 'UserController@profile');
Route::post('/users_profile_update', 'UserController@profile_update');

Route::get('/installs', 'InstallController@index');
Route::get('/installs/add', 'InstallController@add');
Route::get('/installs/view/{id}', 'InstallController@view');
Route::post('/installs/save', 'InstallController@save');
Route::post('/installs/update/{id}', 'InstallController@update');
Route::get('/installs/delete/{id}', 'InstallController@delete');

Route::get('/servers', 'ServerController@index');
Route::get('/servers/add', 'ServerController@add');
Route::get('/servers/view/{id}', 'ServerController@view');
Route::post('/servers/save', 'ServerController@save');
Route::post('/servers/update/{id}', 'ServerController@update');
Route::get('/servers/delete/{id}', 'ServerController@delete');

Route::get('/nodes', 'NodeController@index');
Route::get('/nodes/reboot/{id}', 'NodeController@reboot');
Route::post('/nodes/add_key', 'NodeController@add_key');
Route::post('/nodes/add_node', 'NodeController@add_node');
Route::post('/nodes/update-node-client/{id}', 'NodeController@update_node_client');

Route::get('/clients', 'ClientController@index');
Route::get('/clients/view/{id}', 'ClientController@view');
Route::post('/clients/save', 'ClientController@save');
Route::post('/clients/update/{id}', 'ClientController@update');

Route::get('/hypertargeting', 'HyperTargetingController@index');
Route::post('/hypertargeting/save', 'HyperTargetingController@save');
Route::get('/hypertargeting/view/{id}', 'HyperTargetingController@view');
Route::get('/hypertargeting/delete/{id}', 'HyperTargetingController@delete');
Route::post('/hypertargeting/update/{id}', 'HyperTargetingController@update');
Route::get('/hypertargeting/duplicate/{id}/{new_repo_name}', 'HyperTargetingController@duplicate');

Route::post('/client-credentials/save', 'ClientCredentialController@save');
Route::post('/client-credentials/update/{id}', 'ClientCredentialController@update');
Route::post('/client-credentials/delete/{id}', 'ClientCredentialController@delete');
Route::post('/client-credentials/update-sort', 'ClientCredentialController@update_sort');

Route::post('/client-contacts/save', 'ClientContactController@save');
Route::post('/client-contacts/update/{id}', 'ClientContactController@update');
Route::get('/client-contacts/delete/{id}', 'ClientContactController@delete');

Route::post('/client-notes/save/{author_id?}', 'ClientNoteController@save');
Route::post('/client-notes/update/{id}', 'ClientNoteController@update');
Route::post('/client-notes/delete/{id}', 'ClientNoteController@delete');

Route::get('/owners', 'OwnerController@index');
Route::get('/owners/add', 'OwnerController@add');
Route::get('/owners/view/{id}', 'OwnerController@view');
Route::post('/owners/save', 'OwnerController@save');
Route::post('/owners/update/{id}', 'OwnerController@update');

Route::get('/businesses', 'BusinessController@index');
Route::get('/businesses/geocode', 'BusinessController@geocode');
Route::get('/businesses/add', 'BusinessController@add');
Route::get('/businesses/view/{id}', 'BusinessController@view');
Route::post('/businesses/save', 'BusinessController@save');
Route::post('/businesses/update/{id}', 'BusinessController@update');
Route::get('/businesses/queue_scan/{id}', 'BusinessController@queue_scan');

Route::get('/import-routes', 'ToolController@import_routes');

Route::get('/users', 'UserController@index');
Route::get('/users/add', 'UserController@add');
Route::post('/users/save', 'UserController@save');
Route::get('/users/view/{id}', 'UserController@view');
Route::post('/users/update/{id}', 'UserController@update');
Route::get('/users/delete/{id}', 'UserController@delete');

Route::get('/leads', 'LeadController@index');
Route::get('/leads/add', 'LeadController@add');
Route::post('/leads/save', 'LeadController@save');
Route::get('/leads/view/{id}', 'LeadController@view');
Route::post('/leads/update/{id}', 'LeadController@update');
Route::get('/leads/delete/{id}', 'LeadController@delete');

Route::get('/adas', 'AdaController@index');
Route::post('/adas/save', 'AdaController@save');

Route::get('/tools/plugins', 'ToolController@plugins_index');
Route::get('/tools/plugins/add', 'ToolController@plugins_add');
Route::post('/tools/plugins/save', 'ToolController@plugins_save');
Route::get('/tools/plugins/view/{id}', 'ToolController@plugins_view');
Route::post('/tools/plugins/update/{id}', 'ToolController@plugins_update');
Route::get('/tools/plugins/delete/{id}', 'ToolController@plugins_delete');

Route::get('/tools/repo_types', 'ToolController@repo_types_index');
Route::get('/tools/repo_types/add', 'ToolController@repo_types_add');
Route::post('/tools/repo_types/save', 'ToolController@repo_types_save');
Route::get('/tools/repo_types/view/{id}', 'ToolController@repo_types_view');
Route::post('/tools/repo_types/update/{id}', 'ToolController@repo_types_update');
Route::get('/tools/repo_types/delete/{id}', 'ToolController@repo_types_delete');

Route::get('/tools/repos', 'ToolController@repos_index');
Route::get('/tools/repos/add', 'ToolController@repos_add');
Route::post('/tools/repos/save', 'ToolController@repos_save');
Route::get('/tools/repos/view/{id}', 'ToolController@repos_view');
Route::post('/tools/repos/update/{id}', 'ToolController@repos_update');
Route::get('/tools/repos/delete/{id}', 'ToolController@repos_delete');

Route::prefix('tools')->group(function() {

    Route::get('/client-categories', 'ClientCategoryController@index');
    Route::get('/client-categories/add', 'ClientCategoryController@add');
    Route::get('/client-categories/view/{id}', 'ClientCategoryController@view');
    Route::post('/client-categories/save', 'ClientCategoryController@save');
    Route::post('/client-categories/update/{id}', 'ClientCategoryController@update');
    Route::get('/client-categories/delete/{id}', 'ClientCategoryController@delete');

});

Route::get('/tools/roles', 'ToolController@roles_index');
Route::get('/tools/roles/add', 'ToolController@roles_add');
Route::post('/tools/roles/save', 'ToolController@roles_save');
Route::get('/tools/roles/view/{id}', 'ToolController@roles_view');
Route::post('/tools/roles/update/{id}', 'ToolController@roles_update');
Route::get('/tools/roles/delete/{id}', 'ToolController@roles_delete');

Route::get('/tools/ajax_plugins_by_bbsource', 'ToolController@ajax_plugins_by_bbsource');
Route::get('/tools/ajax_ms_by_client', 'ToolController@ajax_ms_by_client');

// messaging services
Route::get('/tools/messaging_services', 'ToolController@messaging_services_index');
Route::get('/tools/messaging_services/view/{id}', 'ToolController@messaging_services_view');
Route::post('/tools/messaging_services/save', 'ToolController@messaging_services_save');
Route::post('/tools/messaging_services/add_number', 'ToolController@messaging_services_add_number');
Route::post('/tools/messaging_services/update/{id}', 'ToolController@messaging_services_update');

// sms conversations
Route::get('/tools/sms_conversations', 'ToolController@sms_conversations_index');
Route::get('/tools/sms_conversations/add', 'ToolController@sms_conversations_add');
Route::post('/tools/sms_conversations/save', 'ToolController@sms_conversations_save');
Route::post('/tools/sms_conversations/save_script', 'ToolController@sms_conversations_save_script');
Route::get('/tools/sms_conversations/view/{id}', 'ToolController@sms_conversations_view');
Route::post('/tools/sms_conversations/update/{id}', 'ToolController@sms_conversations_update');
Route::post('/tools/sms_conversations/update_script/{id}', 'ToolController@sms_conversations_update_script');
Route::get('/tools/sms_conversations/delete/{id}', 'ToolController@sms_conversations_delete');
Route::get('/tools/sms_conversations/edit_script/{id}', 'ToolController@sms_conversations_edit_script');
Route::get('/tools/sms_conversations/delete_script/{id}', 'ToolController@sms_conversations_delete_script');
Route::get('/tools/sms_conversation_threads/{id}', 'ToolController@sms_conversation_threads');
Route::get('/tools/sms_conversation_users/{id}', 'ToolController@sms_conversation_users');
Route::get('/tools/sms_conversation_thread_view/{id}', 'ToolController@sms_conversation_thread_view');

Route::get('/api/incoming_yext_scan_email', 'ApiController@incoming_yext_scan_email');
Route::post('/api/incoming_yext_scan_email', 'ApiController@incoming_yext_scan_email');
Route::get('/api/cron_messaging_services', 'ApiController@cron_messaging_services');
Route::get('/api/map_leads', 'ApiController@map_leads');
Route::get('/api/yext_scan', 'ApiController@yext_scan');
Route::get('/api/yext_results/{id}', 'ApiController@yext_results');
Route::get('/api/google_name_lookup', 'ApiController@google_name_lookup');

Route::post('/sms/convo_hello', 'SmsController@convo_hello');
Route::post('/sms/incoming_sms', 'SmsController@incoming_sms');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
