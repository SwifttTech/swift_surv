<?php

use Weza\Router;
use Weza\Exceptions\Handler;
use Weza\Middleware\Authenticate;
use Pecee\Http\Middleware\BaseCsrfVerifier;
use Weza\Middleware\RedirectIfAuthenticated;

/**
 * ,------,
 * | NOTE | CSRF Tokens are checked on all PUT, POST and GET requests. It
 * '------' should be passed in a hidden field named "csrf-token" or a header
 *          (in the case of AJAX without credentials) called "X-CSRF-TOKEN"
 *  */
// Router::csrfVerifier(new BaseCsrfVerifier());

// Router::group(array(
//     'prefix' => '/'
// ), function()
// {
    
    Router::group(array(
        'exceptionHandler' => Handler::class
    ), function()
    {
        
        Router::group(array(
            'middleware' => Weza\Middleware\Authenticate::class
        ), function()
        {
            //Dashboard
            Router::get('/', 'Dashboard@get');
            
            //Surveys
            Router::get('/surveys', 'Surveys@get');
            Router::get('/survey/{id}', 'Surveys@show');
            Router::get('/survey/questions/{id}', 'Surveys@surveyQuestions');
            Router::get('/survey/responses/{id}', 'Surveys@responses');
            
            //Questions
            Router::post('/surveys/addQuestion', 'Surveys@addQuestion');
            Router::post('/question/delete', 'Surveys@deleteQuestion');
            Router::post('/question/update', 'Surveys@updateQuestion');
            Router::post('/question/update/view', 'Surveys@updateQuestionview');
            
            //Bulk Campaigns
            Router::post('/surveys/addCampaign', 'Surveys@addCampaign');
            Router::post('/campaign/delete', 'Surveys@deleteCampaign');
            Router::post('/campaign/update', 'Surveys@updateCampaign');
            Router::post('/campaign/update/view', 'Surveys@updateCampaignview');
            
            //Surveys
            Router::post('/surveys/add', 'Surveys@addSurvey');
            Router::post('/surveys/deleteResponse', 'Surveys@deleteResponse');
            Router::post('/survey/delete', 'Surveys@deleteSurvey');
            Router::post('/survey/update', 'Surveys@updateSurvey');
            Router::post('/survey/update/view', 'Surveys@updateSurveyview');
            
            Router::get('/responses/ussd', 'Responses@ussd');
            Router::post('/responses/filter/ussd', 'Responses@ussdFilter');

            Router::get('/responses/sms', 'Responses@sms');
            Router::post('/responses/filter/sms', 'Responses@smsFilter');
            
            Router::get('/responses/sms/{survey}', 'Responses@smsBySurvey');
            Router::post('/responses/filter/sms/{survey}', 'Responses@smsFilterBySurvey');
            
            //Users
            Router::get('/users', 'Profile@users');
            Router::post('/users/delete', 'Profile@delete');
            Router::post('/users/update', 'Profile@updateUser');
            Router::post('/users/changeStatus', 'Profile@changeStatus');
            Router::post('/users/update/view', 'Profile@updateview');

            // settings
            Router::get('/settings', 'Auth@profile');
            Router::post('/create/users', 'Profile@add');
            Router::post('/settings/update/profile', 'Settings@updateprofile');
            Router::post('/settings/update/password', 'Settings@updatepassword');
            
            
            Router::get('/send', 'SMS@get');
            Router::post('/recipients', 'SMS@source');
            Router::post('/send/contacts', 'SMS@sendFromContacts');
            
            Router::post('/approve/campaign', 'SMS@approveCampaign');
            
            Router::post('/send/groups', 'SMS@sendFromGroups');
            Router::post('/send/copy', 'SMS@sendFromCopy');
            Router::post('/send/import', 'SMS@sendFromFile');
            Router::post('/preview/import', 'SMS@previewFile');
            
            Router::get('/campaigns', 'SMS@campaigns');
            Router::get('/campaign/{id}', 'SMS@campaignDetails');

            Router::get('/manual/upload', 'SMS@manualUpload');
            Router::post('/upload', 'SMS@Upload');
            
            Router::get('/settings/roles', 'Settings@roles');
            Router::post('/settings/role/add', 'Settings@addRole');
            Router::post('/settings/delete/role', 'Settings@deleteRole');
            Router::get('/settings/role/permissions/{id}', 'Settings@roleSettings');
            Router::post('/settings/permissions/update', 'Settings@updatePermissions');


            //Signout
            Router::get('/signout', 'Auth@signout');
        });
        
        Router::group(array(
            'middleware' => Weza\Middleware\RedirectIfAuthenticated::class
        ), function()
        {
            
            /**
             * No login Required pages
             **/
            Router::get('/signin', 'Auth@get');
            Router::get('/forgot-password', 'Auth@forgot_password_view');
            Router::get('/register', 'Auth@registerview');

            Router::post('/reset', 'Auth@reset');
            Router::post('/forgot/password', 'Auth@forgotAction');
            Router::get('/reset/{token}', 'Auth@resetview', array(
                'as' => 'token'
            ));
            Router::post('/signin/authenticate', 'Auth@signin');
            Router::post('/signup', 'Auth@signup');
            

            
        });
        // error pages
        Router::get('/404', function()
        {
            response()->httpCode(404);
            echo view();
        });
        Router::get('/405', function()
        {
            response()->httpCode(405);
            echo view('errors/405');
        });
        Router::post('/updateResponses', 'Responses@updateResponses');
        Router::post('/updateResponsesNyeri', 'Responses@updateResponsesNyeri');
        Router::post('/receiveMO', 'Responses@receiveMO');
        Router::get('/loadSMS', 'SMS@loadSMS');
        Router::post('/receiveDLR', 'SMS@receiveDLR');
        Router::post('/testTPS', 'SMS@testTPS');
        Router::get('/manualMO', 'Responses@manualMO');
    });
    
// });
