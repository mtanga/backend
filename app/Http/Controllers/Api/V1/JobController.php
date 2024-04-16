<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Role;
use App\Models\School;
use App\Models\Day;
use App\Models\Language;
use App\Models\Category;
use App\Models\Job;
use App\Models\Task;
use App\Models\Enterprise;
use App\Models\Studentlanguage;
use App\Models\Kill;
use App\Models\Jobday;
use App\Models\Killstudent;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\Bookmark;
use App\Models\Candidature;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailNotify;
use App\Http\Controllers\Api\V1\UserController;


class JobController extends BaseController
{



    public function job_options(){
            return response()->json([
                'message' => 'Connexion failed',
                'status' => '200',
                'days' => Day::all(),
                'domains' => Category::all()
            ], 200); 
    }

    public function add_job(Request $request){
        
        
        if($request->domaine['id']==0){
            $category = new Category;
            $category->title = $request->domaine['name'];
            $category->save();
        }


        //return 0;
        $job = new Job;
        $job->title = $request->title;
        $job->presentation = $request->description;
        $job->salary = $request->price;
        $job->places = $request->places;
        $job->start = $request->start;
        $job->address = $request->address;
        $job->end = $request->end;
        if($request->image){
            $job->image = (new UserController)->saveImage($request->image);
        }
        if($request->domaine['id']==0){
            $job->category_id = $category->id;
        }
        else{
            $job->category_id = $request->domaine['id'];
        }
        $job->user_id = $request->user;
        $job->enterprise_id = $request->enterprise;
        $job->save();

        foreach($request->tasks as $tache){
            $task = new Task;
            $task->status = 0;
            $task->title = $tache;
            $task->job_id = $job->id;
            $task->save();
        }

        foreach($request->available as $dayItem){
            $day = new Jobday;
            $day->start = $dayItem['start'];
            $day->end = $dayItem['end'];
            $day->day = $dayItem['day'];
            $day->job_id = $job->id;
            $day->save();
        }

        return response()->json([
            'message' => 'Connexion failed',
            'status' => '200',
            'job' => $job,
        ], 200); 
    }



    public function getjobs(Request $request){
        return response()->json([
            'message' => 'Connexion failed',
            'status' => '200',
            'data' => Job::query()->where("user_id", $request->id)->with("days", "tasks", "enterprise", "candidatures")->get()
        ], 200); 
}


public function candidatures(Request $request){
    return response()->json([
        'message' => 'Connexion failed',
        'status' => '200',
        'data' => Candidature::query()->where("user_id", $request->id)->with("job", "tasks", "enterprise")->get()
    ], 200); 
}

public function my_students(Request $request){
    return response()->json([
        'message' => 'Connexion failed',
        'status' => '200',
        'data' => Candidature::query()->where("enterprise_id", $request->id)->where("status", '1')->with("job", "tasks", "student")->get()
    ], 200); 
}


public function own_jobs(Request $request){
    return response()->json([
        'message' => 'Connexion failed',
        'status' => '200',
        'data' => Candidature::query()->where("user_id", $request->id)->where("status", '1')->with("job", "tasks", "enterprise")->get()
    ], 200); 
}
   

public function bookmarks(Request $request){
    return response()->json([
        'message' => 'Connexion failed',
        'status' => '200',
        'data' => Bookmark::query()->where("user_id", $request->id)->with("job", "enterprise")->get()
    ], 200); 
}
   


public function delete_job(Request $request){
    $job = Job::query()->find($request->id);
    $job->delete();
    return response()->json([
        'message' => 'Connexion failed',
        'status' => '200',
        'data' => "ok"
    ], 200); 
}

public function get_job_with_options(Request $request){
    return response()->json([
        'message' => 'Connexion failed',
        'status' => '200',
        'days' => Day::all(),
        'domains' => Category::all(),
        'job' => Job::query()->where("id", $request->id)->with("days", "tasks", "enterprise", "user")->first(),
        'candidatures' => Candidature::query()->where("job_id", $request->id)->with("student")->get(),
        'related' => Job::query()->where("id", "!=", $request->id)->with("days", "tasks", "enterprise")->get()
    ], 200); 
}


public function jobs(Request $request){
    return response()->json([
        'message' => 'Connexion failed',
        'status' => '200',
        'days' => Day::all(),
        'domains' => Category::all(),
        'jobs' => Job::query()->with("days", "tasks", "enterprise")->get()
    ], 200); 
}



public function get_enterprise(Request $request){
    return response()->json([
        'message' => 'Connexion failed',
        'status' => '200',
        'enterprise' => Enterprise::query()->where("id", $request->id)->with("user")->first(),
        'jobs' => Job::query()->where("enterprise_id", $request->id)->with("days", "tasks", "enterprise")->get()
    ], 200); 
}

public function add_bookmark(Request $request){
    $bookmark = Bookmark::query()->where("user_id", $request->user)->where("job_id", $request->job)->get();
    $status = 0;
    $job = $bookmark;
    if(count($bookmark)==0){
        $status = 1;
        $job = new Bookmark;
        $job->user_id = $request->user;
        $job->student_id = $request->student;
        $job->enterprise_id = $request->enterprise;
        $job->job_id = $request->job;
        $job->save();
    }
    return response()->json([
        'message' => 'Connexion failed',
        'status' => $status,
        'bookmark' => $job
    ], 200);
}


public function apply(Request $request){
    $bookmark = Candidature::query()->where("user_id", $request->user)->where("job_id", $request->job)->get();
    $status = 0;
    $job = $bookmark;
    if(count($bookmark)==0){
        $status = 1;
        $job = new Candidature;
        $job->user_id = $request->user;
        $job->status = 0;
        $job->student_id = $request->student;
        $job->enterprise_id = $request->enterprise;
        $job->job_id = $request->job;
        $job->save();
    }
    return response()->json([
        'message' => 'Connexion failed',
        'status' => $status,
        'job' => $job
    ], 200);
}



public function accept(Request $request){
    $job = Candidature::query()->find($request->id);
    $job->status = "1";
    $job->update();
    return response()->json([
        'message' => 'Connexion failed',
        'status' => "ok",
        'job' => $job
    ], 200);
}
  
public function deny(Request $request){
    $job = Candidature::query()->find($request->id);
    $job->status = "2";
    $job->update();
    return response()->json([
        'message' => 'Connexion failed',
        'status' => "ok",
        'job' => $job
    ], 200);
}
  
      
public function delete_candidature(Request $request){
    $job = Candidature::query()->find($request->id);
    $job->delete();
    return response()->json([
        'message' => 'Connexion failed',
        'status' => '200',
        'data' => "ok"
    ], 200); 
}

public function delete_bookmark(Request $request){
    $job = Bookmark::query()->find($request->id);
    $job->delete();
    return response()->json([
        'message' => 'Connexion failed',
        'status' => '200',
        'data' => "ok"
    ], 200); 
}
    









}