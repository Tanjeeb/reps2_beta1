<?php

namespace App\Http\Controllers;

use App\{InterviewQuestion, InterviewUserAnswers};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterviewQuestionController extends Controller
{
    /**
     * Set user answer to question and return result of interview
     *
     * @param Request $request
     * @param $question_id
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function setAnswer(Request $request, $question_id)
    {
        $question = InterviewQuestion::find($question_id);

        if (!$question){
            return back(404);
        }

        if(!$request->has('answer_id')){
            return ['error' => 'Не выбран ответ'];
        }

        $data = [
            'question_id'   => $question_id,
            'answer_id'     => $request->get('answer_id')
        ];

        if (Auth::user()){
            $data['user_id'] = Auth::id();
        }

        InterviewUserAnswers::create($data);

        return  view('sidebar-widgets.answers-result')->with('answers',InterviewQuestion::getAnswerQuestion($question_id));
    }

    /**
     * Get result data
     *
     * @param $question_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getResult($question_id)
    {
        return view('sidebar-widgets.answers-result')->with('answers', InterviewQuestion::getAnswerQuestion($question_id));
    }
}
