<?php
namespace Weza\Controllers;

use Weza\Auth;
use Weza\Database;
use Carbon\Carbon;

class Dashboard{

    /**
     * Get dashboard view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $today = date('Y-m-d');
        $month = date('Y-m');
        $year = date('Y');

        $user = Auth::user();
        
        $surveys = Database::table('surveys')->get();
        foreach($surveys as $survey){
        
        $survey->complete = Database::query('select count(*) as count from survey_sessions where survey='.$survey->id.' and status="1"');
        $survey->incomplete = Database::query('select count(*) as count from survey_sessions where survey='.$survey->id.' and status="0"');
        $survey->total = Database::query('select count(*) as count from survey_sessions where survey='.$survey->id.'');
        }
        
        $ussdCount = Database::query('select count(*) as count from responses where channel="ussd"');
        $ussdCountToday = Database::query('select count(*) as count from responses where channel="ussd" and created_at like "'.$today.'%"');
        $ussdCountMonth = Database::query('select count(*) as count from responses where channel="ussd" and created_at like "'.$month.'%"');
        $ussdCountYear = Database::query('select count(*) as count from responses where channel="ussd" and created_at like "'.$year.'%"');
        $incompleteUssd = Database::query('select count(*) as count from responses where channel="ussd" and status="0"');
        $completeUssd = Database::query('select count(*) as count from responses where channel="ussd" and status="1"');
        
        $smsCountToday = Database::query('select count(*) as count from survey_sessions where created_at like "'.$today.'%"');
        $smsCountMonth = Database::query('select count(*) as count from survey_sessions where created_at like "'.$month.'%"');
        $smsCountYear = Database::query('select count(*) as count from survey_sessions where created_at like "'.$year.'%"');
        $incompleteSms = Database::query('select count(*) as count from survey_sessions where status="0"');
        $completeSms = Database::query('select count(*) as count from survey_sessions where status="1"');

        $balance = Database::table('accounts')->where(['id'=>1])->get();

        return view('dashboard', compact("surveys","balance","user","$ussdCount", "ussdCountToday", "ussdCountMonth", "ussdCountYear", "incompleteUssd", "completeUssd","smsCountToday","smsCountMonth","smsCountYear","incompleteSms","completeSms"));

    }
}
