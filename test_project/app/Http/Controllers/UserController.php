<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleValidationRequest;
use App\Http\Requests\CommentValidationRequest;
use App\Http\Requests\LoginValidationRequest;
use App\Http\Requests\ProfileValidationRequest;
use App\Http\Requests\RegisterValidationRequest;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(RegisterValidationRequest $req){
        try{
            $user = new User();
            $user->email = $req->email;
            $user->phone = $req->phone;
            $user->password = Hash::make($req->password);
            $user->save();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['message'=>'registered successfully','data'=>$user,'access_token'=>$token,'token_type'=>'Bearer']);
        }catch(Exception $e){
            return response()->json(['message'=>'something went wrong','data'=>$e],400);
        }
    }

    public function login(LoginValidationRequest $req){
        try{
            if(!Auth::attempt($req->only('email','password'))){
                return response()->json(['message'=>'Unauthorized'],401);
            }
            $user = User::where('email',$req->email)->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['message'=>'logged successfully','data'=>$user,'access_token'=>$token,'token_type'=>'Bearer']);
        }catch(Exception $e){
            return response()->json(['message'=>'something went wrong','data'=>$e],400);
        }
    }

    public function updateProfile(ProfileValidationRequest $req){
        try{
            $logged = Auth::user();
            $user = User::find($logged->id);
            $user->first_name = $req->first_name;
            $user->last_name = $req->last_name;
            $user->role = $req->role;
            $user->save();
            return response()->json(['message'=>'profile updated','data'=>$user]);
        }catch(Exception $e){
            return response()->json(['message'=>'something went wrong','data'=>$e],400);
        }
    }

    public function getProfile(){
        $logged = Auth::user();
        $user = User::select('first_name','last_name','role')->where('id',$logged->id)->first();
        return response()->json(['message'=>'profile detail','data'=>$user]);
    }

    public function createPost(ArticleValidationRequest $req){
        try{
            $logged = Auth::user();
            $article = new Article();
            $article->title = $req->title;
            $article->post = $req->post;
            $article->user_id = $logged->id;
            $article->save();
            return response()->json(['message'=>'post saved successfully','data'=>$article]);
        }catch(Exception $e){
            return response()->json(['message'=>'something went wrong','data'=>$e],400);
        }
    }

    public function updatePost(ArticleValidationRequest $req,$articleId){
        try{
            $logged = Auth::user();
            $article = Article::find($articleId);
            if(is_null($article)){
                return response()->json(['message'=>'post not found'],401);
            }
            $article->title = $req->title;
            $article->post = $req->post;
            $article->save();
            return response()->json(['message'=>'post updated successfully','data'=>$article]);
        }catch(Exception $e){
            return response()->json(['message'=>'something went wrong','data'=>$e],400);
        }
    }

    public function getPost(){
        $logged = Auth::user();
        $article = Article::with('comments');
        if($logged->role == 'writer'){
            $article = $article->where('user_id',$logged->id);
        }
        $article = $article->get();
        return response()->json(['message'=>'post list','data'=>$article]);
    }

    public function getSinglePost($articleId){
        $logged = Auth::user();
        $article = Article::with('comments')->where('id',$articleId);
        if($logged->role == 'writer'){
            $article = $article->where('user_id',$logged->id);
        }
        $article = $article->first();
        if(is_null($article)){
            return response()->json(['message'=>'post not found'],401);
        }
        return response()->json(['message'=>'post','data'=>$article]);
    }

    public function deletePost($articleId){
        try{
            $logged = Auth::user();
            $article = Article::where('user_id',$logged->id)->where('id',$articleId)->delete();
            if($article>0){
                return response()->json(['message'=>'post deleted']);
            }else{
                return response()->json(['message'=>'post not found'],401);
            }
        }catch(Exception $e){
            return response()->json(['message'=>'something went wrong','data'=>$e],400);
        }
    }

    public function addComment(CommentValidationRequest $req){
        try{
            $logged = Auth::user();
            $comment = new Comment();
            $comment->user_id = $logged->id;
            $comment->article_id = $req->post_id;
            $comment->comment = $req->comment;
            $comment->save();
            return response()->json(['message'=>'comment added successfully','data'=>$comment]);
        }catch(Exception $e){
            return response()->json(['message'=>'something went wrong','data'=>$e],400);
        }
    }

    // public function getComments($articleId){
    //     $logged = Auth::user();
    //     return Comment::select('comments.*','articles')->with('article')->where()
    // }

}
