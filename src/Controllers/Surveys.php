<?php
namespace Weza\Controllers;

use Weza\Auth;
use Weza\Database;
use Weza\File;

class Surveys {
    
    /**
     * Get surveys view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user    = Auth::user();
        $role = Database::table('roles')->where('id',$user->role_id)->first();
        $user->role = $role->viewSurveys;
        
        if($user->role == '1'){
        $surveys       = Database::table('surveys')->orderBy('status','desc')->limit(1000)->get();
        $title = 'Surveys';
        return view('surveys', compact("user", "surveys","title"));
        }else{
            return redirect(url('dashboard@get'));
        }
    }
    public function show($id) {
        $user        = Auth::user();
        $role = Database::table('roles')->where('id',$user->role_id)->first();
        $user->role = $role->viewSurveys;
        
        if($user->role == '1'){
        $survey = Database::table('surveys')->where(['id'=>$id])->first();
        $campaigns       = Database::table('bulk_campaigns')->where(['survey'=>$id])->get();
        $title = 'Survey Campaigns';
        return view('survey-campaigns', compact("user", "id","survey","campaigns","title"));
        }else{
            return redirect(url('dashboard@get'));
        }
    }
    
    public function surveyQuestions($id) {
        $user        = Auth::user();
        $role = Database::table('roles')->where('id',$user->role_id)->first();
        $user->role = $role->editSurveys;
        
        if($user->role == '1'){
            $survey = Database::table('surveys')->where(['id'=>$id])->first();
            $questions       = Database::table('survey_questions')->where(['survey'=>$id])->limit(1000)->get();
            $title = $survey->name.' Questions';
            return view('survey-questions', compact("user","id", "survey","questions","title"));
        }else{
                return redirect(url('dashboard@get'));
            }
    }
    
    public static function getSurveys() {
        $user        = Auth::user();
        $surveys = Database::table('surveys')->get();
        
        return $surveys;
    }
    
    public function responses($id) {
        $user        = Auth::user();
        $survey = Database::table('surveys')->where(['id'=>$id])->first();
        $responses       = Database::table('survey_sessions')->where(['survey'=>$id])->orderBy('id', false)->get();
        
        $fields = Database::table('survey_questions')->where(['survey'=>$id])->get();
        
        $fieldNames = [];
        $fieldValues = [];
        
        for($i=0;$i<count($fields)-1;$i++){
            // if(!empty($fields[$i]->name)){
            $ii = $i+1;
                array_push($fieldNames,$fields[$i]->name);
                array_push($fieldValues,'field'.$ii);
            // }[{"field1":"pressing_issue"},{"field2":"governor"}]
        }
        // $fieldNames = json_encode($fieldNames);
        $title = $survey->name.' Responses';
        return view('survey-responses', compact("user","fieldNames", "fieldValues","survey","responses","title"));
    }
    /**
     * Add fleet
     * 
     * @return Json
     */
    public function addQuestion() {
        $user = Auth::user();
        $data = array(
            'name' => escape(input('name')),
            'question' => escape(input('question')),
            'survey' => escape(input('survey')),
        );
        if(Database::table('survey_questions')->insert($data)){
            return response()->json(responder("success", "Question added", "Questions successfully added.", "reload()"));
        }else{
            return response()->json(responder("error", "Oops!", "There was an error adding your question. Please try again.", ""));
        }
    }
    
    public function updateQuestionview() {
        $user        = Auth::user();
        $question = Database::table('survey_questions')->where(array(
            'id' => escape(input('id'))
        ))->first();
        
        return view('extras/updatequestion', compact("question", "user"));
    }
    
    public function updateQuestion() {
        $data = array(
            'name' => escape(input('name')),
            'question' => escape(input('question'))
        );
        if(Database::table("survey_questions")->where("id", input("id"))->update($data)){
            return response()->json(responder("success", "Success", "Question successfully updated", "reload()"));
        }else{
            return response()->json(responder("error", "Oops!", "There was an error updating your question. Please try again.", ""));
        }
    }
    
    public function deleteQuestion() {
        Database::table("survey_questions")->where("id", input("id"))->delete();
        return response()->json(responder("success", "Question Deleted", "Question successfully deleted.", "reload()"));
    }
    
    public function deleteResponse() {
        Database::table("survey_sessions")->where("id", input("id"))->delete();
        return response()->json(responder("success", "Response Deleted", "Response successfully deleted.", "reload()"));
    }
    
    public function addCampaign() {
        $user = Auth::user();
        $data = array(
            'campaign_id' => escape(input('campaign_id')),
            'survey' => escape(input('survey')),
        );
        if(Database::table('bulk_campaigns')->insert($data)){
            return response()->json(responder("success", "Campaign added", "Campaign successfully added.", "reload()"));
        }else{
            return response()->json(responder("error", "Oops!", "There was an error adding your campaign. Please try again.", ""));
        }
    }
    
    public function updateCampaignview() {
        $user        = Auth::user();
        $campaign = Database::table('bulk_campaigns')->where(array(
            'id' => escape(input('id'))
        ))->first();
        
        return view('extras/updatecampaign', compact("campaign", "user"));
    }
    
    public function updateCampaign() {
        $data = array(
            'campaign_id' => escape(input('campaign_id'))
        );
        if(Database::table("bulk_campaigns")->where("id", input("id"))->update($data)){
            return response()->json(responder("success", "Success", "Campaign successfully updated", "reload()"));
        }else{
            return response()->json(responder("error", "Oops!", "There was an error updating your campaign. Please try again.", ""));
        }
    }
    
    public function deleteCampaign() {
        Database::table("bulk_campaigns")->where("id", input("id"))->delete();
        return response()->json(responder("success", "Campaign Deleted", "Campaign successfully deleted.", "reload()"));
    }
    
    
    public function addSurvey() {
        $user = Auth::user();
        $data = array(
            'name' => escape(input('name')),
            'keyword' => escape(input('keyword')),
            'status' => escape(input('status')),
        );
        if(Database::table('surveys')->insert($data)){
            if(!empty(input('questions')) && input('questions') > 0){
                $surveyID = Database::table('surveys')->insertId();
                $loadQuestions = Database::query('insert into survey_questions(survey, name, question)SELECT "'.$surveyID.'", name, question FROM survey_questions where survey="'.input('questions').'"');
            }
            return response()->json(responder("success", "Survey added", "Survey successfully added.", "reload()"));
        }else{
            return response()->json(responder("error", "Oops!", "There was an error adding your survey. Please try again.", ""));
        }
    }
    
    public function updateSurveyview() {
        $user        = Auth::user();
        $survey = Database::table('surveys')->where(array(
            'id' => escape(input('id'))
        ))->first();
        
        return view('extras/updatesurvey', compact("survey", "user"));
    }
    
    public function updateSurvey() {
        $data = array(
            'name' => escape(input('name')),
            'keyword' => escape(input('keyword')),
            'status' => escape(input('status')),
        );
        if(Database::table("surveys")->where("id", input("id"))->update($data)){
            return response()->json(responder("success", "Success", "Survey successfully updated", "reload()"));
        }else{
            return response()->json(responder("error", "Oops!", "There was an error updating your survey. Please try again.", ""));
        }
    }
    
    public function deleteSurvey() {
        Database::table("surveys")->where("id", input("id"))->delete();
        return response()->json(responder("success", "Survey Deleted", "Survey successfully deleted.", "reload()"));
    }
    
}