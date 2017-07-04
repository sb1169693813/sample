<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use Mail;
class UsersController extends Controller
{

    public function __construct()
    {
      $this->middleware('auth', [
            'only' => ['edit', 'update', 'destory']
        ]);
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $users = User::paginate(10);
      return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.注册
     *
     * @return \Illuminate\Http\Response
     */
     public function create()
     {
         return view('users.create');
     }

    /**
     * Store a newly created resource in storage.注册操作
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
          'name' => 'required|max:50',
          'email' => 'required|email|unique:users|max:255',
          'password' => 'required'
      ]);
      $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        //注册之后发送邮件
        $this->sendEmailConfirmationTo($user);
        //Auth::login($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
        //return redirect()->route('users.show', [$user]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function show($id)
     {
         $user = User::findOrFail($id);
         return view('users.show', compact('user'));
     }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $user = User::findOrFail($id);
      $this->authorize('update',$user);
      return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'confirmed|min:6'
        ]);
        $user = User::findOrFail($id);
        $this->authorize('update',$user);
        $data = [];
        $data['name'] = $request->name;
        if($request->password)
        {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success','个人资料更新成功！');

        return redirect()->route('users.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('destory', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }


    //laravel发送给邮件
    protected function sendEmailConfirmationTo($user)
    {
      $view = 'emails.confirm';
      $data = compact('user');
      $from = 'aufree@estgroupe.com';
      $name = 'Aufree';
      $to = $user->email;
      $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

      Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject){
        $message->from($from, $name)->to($to)->subject($subject);
      });
    }

    //激活操作
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功');
        return redirect()->route('users.show', [$user]);
    }

}
