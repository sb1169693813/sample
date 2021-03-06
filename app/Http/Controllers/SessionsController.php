<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
class SessionsController extends Controller
{
    // public function __construct()
    // {
    //   $this->authorize('guest', [
    //     'only' =>['create']
    //   ]);
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.登录
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sessions.create');
    }

    /**
     * Store a newly created resource in storage.登录
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
         'email' => 'required|email|max:255',
         'password' => 'required'
     ]);
     $credentials = [
       'email' => $request->email,
       'password' => $request->password
     ];
     if(Auth::attempt($credentials,$request->has('remember')))
     {
       //验证是否激活
       if(Auth::user()->activated)
       {
         session()->flash('success', '欢迎回来！');
         return redirect()->intended(route('users.show', [Auth::user()]));
       }
       else
       {
         Auth::logout();
         session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
         return redirect('/');
       }
     }
     else
     {
        session()->flash('danger','很抱歉，您的邮箱和密码不匹配');
        return redirect()->back();
     }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        Auth::logout();
        session()->flash('success','您已成功退出');
        return redirect()->route('login');
    }
}
