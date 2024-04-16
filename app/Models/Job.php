<?php

namespace App\Models;
use App\Models\Task;
use App\Models\Kill;
use App\Models\Level;
use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \DateTimeInterface;


class Job extends Model
{
    use SoftDeletes;

    use HasFactory;
    
        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'presentation',
        'salary',
        'hours',
        'start',
        'end',
        'category_id',
        'places',
        'Code',
        'enterprise_id'
    ];



    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function candidatures()
    {
        return $this->hasMany(Candidature::class, 'job_id');
    }


    public function enterprise(){
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }


    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'job_id');
    }

    public function kills()
    {
        return $this->hasMany(Kill::class, 'job_id');
    }

    public function days()
    {
        return $this->hasMany(Jobday::class, 'job_id');
    }


    public function levels()
    {
        return $this->hasMany(Level::class, 'job_id');
    }


    public function languages()
    {
        return $this->hasMany(Language::class, 'job_id');
    }


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

}