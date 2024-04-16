<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Role;
use App\Models\School;
use App\Models\Language;
use App\Models\Enterprise;
use App\Models\Studentlanguage;
use App\Models\Kill;
use App\Models\Killstudent;
use Illuminate\Support\Facades\Auth;
use Validator;
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


class UserController extends BaseController
{
    
    public function ping(){
   
        $data["version"] = "1.0.0";
        $data["author"] = "Michel TANGA";
        $data["website"] = "https://micheltanga.com";
        $data["service"] = "Mevo Teams workspace";
        $data["time"] = Carbon::now();
        $data["database"] = "Connected";
        return $data;
    }
    
    public function forgot(Request $request){
        $user = User::query()->where("email", $request->email)->first();
        if($user){


        }
        else{
            return response()->json([
                'message' => 'Connexion failed',
                'status' => '200',
                'user' => $user
            ], 200); 
        }
        
        
    }
    
    public function roles(Request $request){
        return response()->json([
            'object' => "roles",
            'data' => Role::query()->where("code", "!=", "ADMIN")->get()
        ], 200);
    }

    public function verify(Request $request){
        $user = User::query()->where("email", $request->email)->first();
        $user->sendEmailVerificationNotification();
        return response()->json([
            'status' => "200",
            'user' => $user
        ], 200); 

    }

    public function register(Request $request){
        $status = "";
        $email = User::query()->where("email", $request->email)->first();
        if($email){
            $status = "203";
            $user = [];
        }
        else{
            $status = "200";
            do {
                $username = mt_rand(100000,999999);
            } while ( DB::table( 'users' )->where( 'username', $username )->exists() );
    
            $user = new User;
            $user->email =  $request->email;
            $user->username =  $username;
            $user->role_id =  $request->role;
            $user->password =  bcrypt($request->password);
            $user->save();
            

           if($request->role_code=="CANDIDAT"){
                $student = new Student;
                $student->user_id = $user->id;
                $student->save();
            }
            
           if($request->role_code=="ENTEPRISE"){
            $enterprise = new Enterprise;
            $enterprise->user_id = $user->id;
            $enterprise->save();
        }


            $user->sendEmailVerificationNotification();
        }
        return response()->json([
                            'status' => $status,
                            'user' => $user
        ], 200); 
    }
    


    public function login(Request $request){
            $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            if(auth()->attempt(array($fieldType => $request->email, 'password' => $request->password)))
            {
                $user =  Auth::user();

                //check role
                $role = Role::query()->where("id", $user->role_id)->first();
                if($role->code=="ENTEPRISE"){
                    $success['user'] = User::query()->where("id", $user->id)->with("role", "enterprise")->first();
                }
                if($role->code=="CANDIDAT"){
                    $success['user'] = User::query()->where("id", $user->id)->with("role", "student")->first();
                }

                if(!$user->email_verified_at){
                    return response()->json([
                        'message' => 'Connexion reussie',
                        'status' => '204',
                        'object' => "user"
                    ], 200);
                }
                else{
                    $success['token'] =  $user->createToken('MyApp')->plainTextToken;
                    return response()->json([
                            'message' => 'Connexion reussie',
                            'status' => '200',
                            'data' => $success,
                            'object' => "user"
                    ], 200);
                }
            }
            else{
                return response()->json([
                                'message' => 'Connexion failed',
                                'status' => '203'    
                ], 202);  
            }
    }
        
    public function password(Request $request){
        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'id';
        if(auth()->attempt(array($fieldType => $request->id, 'password' => $request->old)))
        {
            $user =  Auth::user();
            return $user;
            if($request->nouveau!=$request->confirmation){
                return response()->json([
                            'message' => 'Connexion failed',
                            'status' => '203'    
            ], 203);  
            }
            else{
                $user = User::query()->find($request->id);
                $user->password = bcrypt($request->password);
                $user->update();
                return response()->json([
                            'message' => 'Connexion failed',
                            'status' => '200'    
            ], 200); 
            }
        }
        else{
            return response()->json([
                            'message' => 'Connexion failed',
                            'status' => '201'    
            ], 201);  
        }
        
    } 
    
    public function update_photo(Request $request){
        $object = [];
        if($request->type=="student"){
            if(!$request->student){
                $user = new Student;
                $user->photo = $this->saveImage($request->photo);
                $user->user_id = $request->user;
                $user->save();
                $object = User::query()->where("id", $user->user_id)->with("role", "student")->first();
            }
            else{

            }
        }
        if($request->type=="enterprise"){
            $user = Enterprise::query()->find($request->enterprise);
            $user->logo = $this->saveImage($request->photo);
            $user->update();
            $object = User::query()->where("id", $user->user_id)->with("role", "enterprise")->first();
        }
        return response()->json([
            'message' => 'photoUser',
            'status' => '200',
            'data' => $object

        ], 200);
    }


    public function update_profile(Request $request){
        $object = [];
        if($request->type=="student"){
            if(!$request->student){
                $user = new Student;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->classe = $request->classe;
                $user->phone = $request->phone;
                $user->address = $request->address;
                $user->birth = $request->birth;
                $user->user_id = $request->user;
                $user->gender = $request->gender;
                $user->school_id = $request->school;
                $user->bio = $request->bio;
                if($request->cv){
                    $user->cv = $this->saveImage($request->cv);
                }
                $user->save();

                if($request->language){
                    $userLanguage1 = Studentlanguage::query()->where("student_id", $user->id)->first();
                    if($userLanguage1){
                        $userLanguageDelete = Studentlanguage::query()->find($userLanguage1->id);
                        $userLanguageDelete->delete();
                    }
                    $language = new Studentlanguage;
                    $language->student_id = $user->id;
                    $language->language_id = $request->language;
                    $language->save();
                }
                $object = User::query()->where("id", $user->user_id)->with("role", "student")->first();
            }
            else{
                $user = Student::query()->find($request->student);
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->classe = $request->classe;
                $user->phone = $request->phone;
                $user->address = $request->address;
                $user->user_id = $request->user;
                $user->birth = $request->birth;
                $user->gender = $request->gender;
                $user->school_id = $request->school;
                $user->bio = $request->bio;
                if($request->cv){
                    $user->cv = $this->saveImage($request->cv);
                }
                $user->update();

                if($request->language){
                    $userLanguage1 = Studentlanguage::query()->where("student_id", $user->id)->first();
                    if($userLanguage1){
                        $userLanguageDelete = Studentlanguage::query()->find($userLanguage1->id);
                        $userLanguageDelete->delete();
                    }
                    $language = new Studentlanguage;
                    $language->student_id = $user->id;
                    $language->language_id = $request->language;
                    $language->save();
                }


                $object = User::query()->where("id", $user->user_id)->with("role", "student")->first();
            }
        }
        if($request->type=="enterprise"){
            $user = Enterprise::query()->find($request->enterprise);
            $user->name = $request->name;
            $user->website = $request->website;
            $user->phone = $request->phone;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->presentation = $request->presentation;
            $user->update();
            $object = User::query()->where("id", $user->user_id)->with("role", "enterprise")->first();
        }
        return response()->json([
            'message' => 'profileUser',
            'status' => '200',
            'data' => $object

        ], 200);
    }


    public function saveImage($myimage){
        if ($myimage) {
            
        $folderPath = "images/users/"; //path location
        $image_parts = explode(";base64,", $myimage);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $file = $folderPath . uniqid() . '.'.$image_type;
        file_put_contents($file, $image_base64);
        return $file;
        
        
            //The base64 encoded image data
            $image_path =  "images/users/"; //path location
            $image_64 = $myimage;
            // exploed the image to get the extension
            $extension = explode(';base64',$image_64);
            //from the first element
            $extension = explode('/',$extension[0]);
            // from the 2nd element
            $extension = $extension[1];
    
            $replace = substr($image_64, 0, strpos($image_64, ',')+1);
    
            // finding the substring from 
            // replace here for example in our case: data:image/png;base64,
            $image = str_replace($replace, '', $image_64);
            // replace
            $image = str_replace(' ', '+', $image);
            // set the image name using the time and a random string plus
            // an extension
            $imageName = time().'_'.Str::random(20).'.'.$extension;
            // save the image in the image path we passed from the 
            // function parameter.
            \Storage::disk('local')->put($imageName, base64_decode($image));
            Storage::disk('public')->put($image_path.'/' .$imageName, base64_decode($image));
            return  $image_path.''.$imageName;
        }
        else{
            return null;
        }
    }

    public function saveCV($myimage){
        if ($myimage) {
            
        $folderPath = "documents/cv"; //path location
        $image_parts = explode(";base64,", $myimage);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $file = $folderPath . uniqid() . '.'.$image_type;
        file_put_contents($file, $image_base64);
        return $file;
        
        
            //The base64 encoded image data
            $image_path =  "documents/cv/"; //path location
            $image_64 = $myimage;
            // exploed the image to get the extension
            $extension = explode(';base64',$image_64);
            //from the first element
            $extension = explode('/',$extension[0]);
            // from the 2nd element
            $extension = $extension[1];
    
            $replace = substr($image_64, 0, strpos($image_64, ',')+1);
    
            // finding the substring from 
            // replace here for example in our case: data:image/png;base64,
            $image = str_replace($replace, '', $image_64);
            // replace
            $image = str_replace(' ', '+', $image);
            // set the image name using the time and a random string plus
            // an extension
            $imageName = time().'_'.Str::random(20).'.'.$extension;
            // save the image in the image path we passed from the 
            // function parameter.
            \Storage::disk('local')->put($imageName, base64_decode($image));
            Storage::disk('public')->put($image_path.'/' .$imageName, base64_decode($image));
            return  $image_path.''.$imageName;
        }
        else{
            return null;
        }
    }


    public function get_profilestudent(Request $request){
        return response()->json([
            'object' => "Schools",
            'schools' => School::all(),
            'languages' => Language::all(),
            'ownschool' => Student::query()->where("id", $request->student)->with("school", "user")->first(),
            'langues' => Studentlanguage::query()->where("student_id", $request->student)->with('languages')->get()
        ], 200);
    }

    

    public function get_profileenterprise(Request $request){
        return response()->json([
            'object' => "Schools",
            'schools' => School::all(),
            'languages' => Language::all(),
            'ownschool' => Student::query()->where("id", $request->student)->with("school", "user")->first(),
            'langues' => Studentlanguage::query()->where("student_id", $request->student)->with('languages')->get()
        ], 200);
    }

    public function getKills(Request $request){
        return response()->json([
            'object' => "kills",
            'kills' => Kill::all(),
            'ownkills' => Killstudent::query()->where("object", $request->user)->where("type", $request->type)->with('kills')->get()
        ], 200);
    }


    public function addkill(Request $request){
        $kill = Kill::query()->where("name", $request->name)->get();
        if(count($kill)==0){
            $newkill = new Kill;
            $newkill->name = $request->name;
            $newkill->save();

            $newkilluser = new Killstudent;
            $newkilluser->object = $request->user;
            $newkilluser->kill_id = $newkill->id;
            $newkilluser->type = $request->type;
            $newkilluser->save();
        }
        else{
            $newkilluser = new Killstudent;
            $newkilluser->object = $request->user;
            $newkilluser->kill_id = $request->kill;
            $newkilluser->type = $request->type;
            $newkilluser->save();
        }
        return response()->json([
            'object' => "kills",
            'kills' => Kill::all(),
            'ownkills' => Killstudent::query()->where("object", $request->user)->where("type", $request->type)->with('kills')->get()
        ], 200);

        
    }


    public function delete_kill(Request $request){
        $kill = Killstudent::query()->find($request->id);
        $kill->delete();
        return response()->json([
            'object' => "kills",
            'kills' => Kill::all(),
            'ownkills' => Killstudent::query()->where("object", $request->user)->where("type", $request->type)->with('kills')->get()
        ], 200);

        
    }

    public function update_password(Request $request){
        $user = User::query()->find($request->user);
        $status = '201';
        if(Auth::guard('web')->attempt(array("email" => $user->email, 'password' => $request->old))){
            $user->password =  bcrypt($request->new);
            $user->update();
            $status = '200';
        }
        return response()->json([
            'message' => 'passwordChange',
            'status' => $status
        ], 200);
        
    }



    public function companies(Request $request){
        return response()->json([
            'object' => "comapanies",
            'data' => Enterprise::query()->with('jobs')->get()
        ], 200);
    }

}