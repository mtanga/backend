<?php

namespace App\Models;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \DateTimeInterface;


class Student extends Model
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
        'classe',
        'photo',
        'bio',
        'phone',
        'user_id',
        'gender',
        'cv',
        'birth',
        'address',
        'school_id'
    ];



    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }




    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }




}