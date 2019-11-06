<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Cache;
use Exception;
use Mail;
use DB;

/**
 * Class UserService
 * @package App\Services
 */
class UserService
{

    /**
     * @var User
     */
    public $user;

    /**
     * UserService constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function query()
    {
        return $this->user->query();
    }

    /**
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findUserById($userId)
    {
        return $this->query()->find($userId);
    }

    /**
     * @param array $search
     * @return $this|\Illuminate\Database\Eloquent\Builder
     */
    public function list(array $search = [])
    {
        $searchString = data_get($search, 'searchString', false);
        $searchStatus = data_get($search, 'searchStatus', false);
        $typeStatus = data_get($search, 'typeStatus', false);
        $query = $this->query();

        if ($searchString) {
            $query = $query->where(function($query) use($searchString) {
                return $query->where('name', 'like', "%{$searchString}%")
                    ->orWhere('email', 'like', "%{$searchString}%");
            });
        }

        if ($searchStatus) {
            $query = $query->where('status_id', $searchStatus);
        }

        if ($typeStatus) {
            $query = $query->where('type_id', $typeStatus);
        }

        return $query;
    }

    private function generatePassword()
    {
        return substr(sha1(time()), 0, 6);
    }

    private function sendPasswordEmail(User $user, $newPassword)
    {
        Mail::send(['html' => 'email.nova_senha'], ['user' => $user, 'password' => $newPassword], function($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Nova senha');
        });

        if (Mail::failures()) {
            return false;
        }

        return true;
    }

    public function sendResetPasswordEmail(User $user, $token)
    {
        Mail::send(['html' => 'email.reset_password'], ['token' => $token], function($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Redefinição de senha');
        });

        if (Mail::failures()) {
            return false;
        }

        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user, $sendPassword = true)
    {
        try {
            DB::transaction(function () use($user, $sendPassword){
                if ($sendPassword){
                    $generatePassword = $this->generatePassword();

                    if (is_null($user->password)) {
                        $user->password = $generatePassword;
                    }

                    $user->password = Hash::make($user->password);
                }

                $user->email = trim($user->email);

                if (is_null($user->api_token)) {
                    $user->api_token = \Illuminate\Support\Str::random(80);
                }

                if (!$user->save())
                    throw new Exception('Erro ao inserir no banco de dados');

                //set Role to user..
                $this->assignRole($user, $user->user_group_id);

                if($sendPassword){
                    if (!$this->sendPasswordEmail($user, $generatePassword))
                        throw new Exception('Erro ao enviar senha ao novo usuario');
                }

                return true;
            });
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function update(User $user)
    {
        return $this->create($user, false);
    }

    /**
     * @param User $user
     * @return bool|null
     */
    public function delete(User $user)
    {
        return $user->delete();
    }

    public function assignRole(User $user, $user_group_id)
    {
        $userGroup = UserGroup::query()->find($user_group_id);

        $user->syncRoles($userGroup->name);

        return true;
    }

}
