<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use HasFactory;
    private $attributes;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->attributesEditor = [
            'email' => 'editor@me.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'phone' => rand(1000000000,9999999999)
        ];

        $this->attributesWriter = [
            'email' => 'writer@me.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'phone' => rand(1000000000,9999999999)
        ];
        

    }

    public function test_register_writer_user(){
        $this->post('api/register', $this->attributesWriter)->assertStatus(200);
    }

    public function test_register_editor_user(){
        $this->post('api/register', $this->attributesEditor)->assertStatus(200);
    }


    // public function test_login_user(){

    //     $formData = [
    //         'email'=>$this->attributesWriter['email'],
    //         'password'=>'123456'];
    //     $this->post('/api/login',$formData)->assertStatus(200);

    // }

    public function test_update_writer_user(){
        
        $user= User::where('email',$this->attributesWriter['email'])->first();
        $this->actingAs($user,'sanctum');

        $formData = [
            'first_name'=>'deekshith',
            'last_name'=>'writer',
            'role'=>'writer'];
        $this->put('/api/update-profile',$formData)->assertStatus(200);

    }

    public function test_update_editor_user(){
        
        $user= User::where('email',$this->attributesEditor['email'])->first();
        $this->actingAs($user,'sanctum');

        $formData = [
            'first_name'=>'deekshith',
            'last_name'=>'editor',
            'role'=>'editor'];
        $this->put('/api/update-profile',$formData)->assertStatus(200);

    }

    public function test_get_profile(){
        
        $user= User::where('email',$this->attributesEditor['email'])->first();
        $this->actingAs($user,'sanctum');

        $this->get('/api/profile-detail')->assertStatus(200);

    }

    public function test_create_article(){
        
        $user= User::where('email',$this->attributesWriter['email'])->first();
        $this->actingAs($user,'sanctum');
        $data = ["title"=>"deekshithhhfd testing article",
        "post"=>"this is my1 3f post this is my 13 post thi hdhdfhgh hf s is my first post "];

        $this->post('/api/create-article',$data)->assertStatus(200);

    }

    public function test_update_article(){
        
        $user= User::where('email',$this->attributesWriter['email'])->first();
        $article = Article::where('user_id',$user->id)->first();
        $this->actingAs($user,'sanctum');
        $data = ["title"=>"testing article updae",
        "post"=>"this is post this is first post "];

        $this->put('/api/update-article/'.$article->id,$data)->assertStatus(200);

    }

    public function test_single_article(){
        
        $user= User::where('email',$this->attributesEditor['email'])->first();
        $article = Article::first();
        $this->actingAs($user,'sanctum');

        $this->get('/api/single-article/'.$article->id)->assertStatus(200);

    }

    public function test_list_article(){
        
        $user= User::where('email',$this->attributesEditor['email'])->first();
        $this->actingAs($user,'sanctum');

        $this->get('/api/list-article/')->assertStatus(200);

    }

    

    public function test_add_comment(){
        
        $user= User::where('email',$this->attributesEditor['email'])->first();
        $user1= User::where('email',$this->attributesWriter['email'])->first();
        $article = Article::where('user_id',$user1->id)->first();
        $this->actingAs($user,'sanctum');

        $data = ["post_id"=>$article->id,"comment"=>"comment comment c5 5 5 55omment comment comment comment comment"];
        $this->post('/api/add-comment/',$data)->assertStatus(200);

    }

    public function test_delete_article(){
        
        $user= User::where('email',$this->attributesWriter['email'])->first();
        $article = Article::where('user_id',$user->id)->first();
        $this->actingAs($user,'sanctum');

        $this->delete('/api/delete-article/'.$article->id)->assertStatus(200);

    }
}
