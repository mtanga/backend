<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \DateTimeInterface;


class Day extends Model
{
    use SoftDeletes;

    use HasFactory;
    
        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];



    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];




}