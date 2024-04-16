<?php

namespace App\Models;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \DateTimeInterface;


class Enterprise extends Model
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
        'address',
        'phone',
        'website',
        'logo',
        'experience',
        'category_id',
        'user_id',
        'presentation'
    ];



    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function days(){

    }


    public function galeries(){
        
    }

        
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'enterprise_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }



}