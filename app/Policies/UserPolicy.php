<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }

//     删除用户的动作，有两个逻辑需要提前考虑：
//
// 只有当前登录用户为管理员才能执行删除操作；
// 删除的用户对象不是自己（即使是管理员也不能自己删自己）。
  public function destory(User $currentUser, User $user)
  {
    return $currentUser->is_admin && $currentUser !== $user->id;
  }
}
