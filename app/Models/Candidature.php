<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \DateTimeInterface;


class Candidature extends Model
{
    use SoftDeletes;

    use HasFactory;
    
        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'student_id',
        'enterprise_id',
        'user_id',
        'status'
    ];



    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function enterprise(){
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    public function job(){
        return $this->belongsTo(Job::class, 'job_id');
    }


    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function student(){
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'job_id', 'job_id');
    }




}