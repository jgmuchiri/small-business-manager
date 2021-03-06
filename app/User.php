<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{

    use Notifiable;

    protected $guard_name = 'web';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'role',
        'phone',
        'first_name',
        'last_name',
        'address',
        'photo',
        'dob',
        'company',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $guarded = [];

    /**
     * read one or more user fields
     *
     * @param $id
     * @param array $items
     * @return string
     */
    public static function read($id, $items)
    {
        $user = self::whereId($id)->first();
        if (count($user) == 0) {
            return '';
        }

        $data = '';
        if (is_array(($items))) {
            foreach ($items as $item) {
                $data .= $user->$item . ' ';
            }
        } else {
            if ($items == 'name'):
                $data = $user->first_name . ' ' . $user->last_name;else:
                $data = $user->$items;
            endif;
        }

        return $data;
    }

    /**
     * @return mixed
     */
    public static function pluckAble()
    {
        $users = self::select('id', DB::raw('CONCAT(first_name, " ", last_name," - ",company) AS name'))->get();
        return $users;
    }
}
