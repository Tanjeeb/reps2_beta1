<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Get user list
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $users = User::with('role', 'country')->withCount('positive', 'negative');

        if ($request->has('search') && null !==$request->get('search')){
            $users->where(function ($query) use ($request)
            {
                $query->where('id', $request->get('search'))
                    ->orWhere('name', 'like', '%'.$request->get('search').'%')
                    ->orWhere('email', 'like', '%'.$request->get('search').'%');
            });
        }

        if ($request->has('country') && null !==$request->get('country')){
            $users->where('country_id', $request->get('country'));
        }

        if ($request->has('email_verified') && null !==$request->get('email_verified')){
            if ($request->get('email_verified') == 0){
                $users->whereNull('email_verified_at');
            } else{
                $users->whereNotNull('email_verified_at');
            }
        }

        if ($request->has('role') && null !==$request->get('role')){
            $users->where('user_role_id', $request->get('role'));
        }

        if($request->has('sort') && null !==$request->get('sort')){
            $users->orderBy($request->get('sort'));
        } else{
            $users->orderBy('created_at');
        }

        $users = $users->paginate(50)->appends($request->all());

        return view('admin.user.user_list')->with(['data'=> $users, 'request_data' => $request->all()]);
    }
}