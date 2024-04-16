<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \DateTimeInterface;


class Admin extends Model
{
    use SoftDeletes;

    use HasFactory;
    
        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'photo',
        'phone',
        'user_id'
    ];



    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }



}