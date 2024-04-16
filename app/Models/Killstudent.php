<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \DateTimeInterface;


class Killstudent extends Model
{
    use SoftDeletes;

    use HasFactory;
    
        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'object',
        'kill_id',
        'type'
    ];



    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function kills()
    {
        //return $this->hasOne(Language::class, 'id', 'language_id');
        return $this->hasMany(Kill::class, 'id', 'kill_id');
    }




}