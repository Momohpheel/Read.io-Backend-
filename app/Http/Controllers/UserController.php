<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\article as Article;
use App\Models\Comment;
use App\Models\React;
use App\Models\User;
use App\Models\Follow;
use App\Models\Book;
use App\Traits\Response;
use Carbon\Carbon;

class UserController extends Controller
{
    use Response;

    public function profile(Request $request){
        $validated = $request->validate([
            'bio' => 'required|string',
            'role' => 'required|string',
        ]);
        try{
            $user = User::find(auth()->user()->id);
            $user->bio =$validated['bio'];
            $user->role =$validated['role'];
            $user->save();

            return $this->success($user, "Profile Added", 201);
        }catch(Exception $e){
        return $this->error($e->getMessage(), "Updating profile failed", 400);
        }
    }
    public function updateProfile(){}

    public function writeArticle(Request $request){
        $validated = $request->validate([
            'article' => 'required|string',
            'type' => 'required|string',
            'title' => 'required|string',
        ]);
            try{

                $article = new Article;
                $article->article =$validated['article'];
                $article->type =$validated['type'];
                $article->title =$validated['title'];
                $article->user_id = auth()->user()->id;
                $article->save();

                return $this->success($article,"Article Sucessfully Added", 201);


            }catch(Exception $e){
                return $this->error($e->getMessage(), "Article couldnt be added", 400);
            }

    }


    public function react($id){

        try{

            $post = Article::find($id);
            $react = React::where('liker', auth()->user()->id)->where('post_id', $post->id)->first();
            if ($react){
                $react->delete();
                $post->love = $post->love - 1;
                $post->save();

                return $this->success($post, "You just unliked this post", 200);
            }
            else{
                $react = new React();
                $react->liker = auth()->user()->id;
                $react->post_id = $id;
                $react->save();

                $post->love = $post->love + 1;
                $post->save();

                return $this->success($post, "You just liked this post", 200);
            }



        }catch(Exception $e){return $this->error($e->getMessage(), "You couldnt react to this post", 400);}

    }

    public function updateArticle(Request $request, $id){

        $validated = $request->validate([
            'article' => 'required|string',
            'type' => 'required|string',
            'title' => 'required|string',
        ]);
            try{
                $article = Article::where('id', $id)->where('user_id', auth()->user()->id)->first();
                $article->article =$validated['article'];
                $article->type =$validated['type'];
                $article->title =$validated['title'];
                $article->save();

                return $this->success($article,"Article Updated", 201);


            }catch(Exception $e){return $this->error($e->getMessage(), "Article Update failed", 400);}

    }

    public function deleteArticle($id){

        try{
            $article = Article::where('id', $id)->where('user_id', auth()->user()->id)->first();
            $article->delete();
            return $this->success([],'Article deleted', 200);
        }catch(Exception $e){return $this->error($e->getMessage(), "Article couldnt be deleted", 400);}

    }

    public function getArticle($id){

        try{

            $article = Article::where('id', $id)->first();
            $data = [
                'article' => $article->article,
                'type' => $article->type,
                'title' => $article->title,
            ];

        return $this->success($data,"Fetched Article", 200);
        }catch(Exception $e){return $this->error($e->getMessage(), "Fetch article failed", 400);}

    }

    public function getAllArticles(){

        try{

            $article = Article::all();
            foreach($article as $read){
                $user = User::where('id', $read->user_id)->first();
                $data = [
                    'article' => $read->article,
                    'type' => $read->type,
                    'title' => $read->title,
                    'writer' => $user->name
                ];
            }

            return $this->success($data,"Fetch all articles", 200);

        }catch(Exception $e){return $this->error($e->getMessage(), "Fetch article failed", 400);}

    }

    public function getUserArticles(){

        try{
            $article = Article::where('user_id', auth()->user()->id)->get();
            foreach($article as $read){
                $user = User::where('id', $read->user_id)->first();
                $data = [
                    'article' => $read->article,
                    'type' => $read->type,
                    'title' => $read->title,
                    'writer' => $user->name
                ];
            }

            return $this->success($data,"User Article Fecthed", 200);
        }catch(Exception $e){return $this->error($e->getMessage(), "Fetch article failed", 400);}

    }


    public function comment(Request $request, $id){
        try{

            $validated = $request->validate([
                'comment' => 'required|string',
            ]);

            $comment = new Comment;
            $comment->comment =$validated['comment'];
            $comment->post_id = $id;
            $comment->commenter = auth()->user()->id;
            $comment->save();

            return $this->success($comment,"Comment Added", 201);
        }catch(Exception $e){return $this->error($e->getMessage(), "Comment couldnt be added", 400);}



    }

    public function follow($id){
        try{


            $user = User::find($id);
        $follow = Follow::where('follower_id', auth()->user()->id)->where('followed_id', $user->id)->first();
        if ($follow){
            $follow->delete();
            return $this->success('You just Unfollowed '.$user->name, 200);
        }
        else{
            $follow = new Follow();
            $follow->follower_id = auth()->user()->id;
            $follow->followed_id = $id;
            $follow->save();

            return $this->success('You just Followed '.$user->name, 200);
        }


        }catch(Exception $e){return $this->error($e->getMessage(), "Following user failed", 400);}




    }


    public function filterArticles($type){
        try{
            $article = Article::where('type', $type)->get();
            foreach($article as $read){
                $user = User::where('id', $read->user_id)->first();
                $data = [
                    'article' => $read->article,
                    'type' => $read->type,
                    'title' => $read->title,
                    'writer' => $user->name
                ];
            }

        return $this->success($data, "Filtered Artcle", 200);

        }catch(Exception $e){return $this->error($e->getMessage(), "Filtering article failed", 400);}



    }

    public function getBooks(){
        try{


            $books = Book::all();
            foreach($books as $book){
                $data = [
                    'book_name' => $book->book_name,
                    'description' => $book->description,
                    'category' => $book->category,
                    'book_picture' => $book->book_picture,

                ];
            }

            return $this->success($data,"Books fetched", 200);
        }catch(Exception $e){return $this->error($e->getMessage(), "Fetch books failed", 400);}

    }

    public function uploadBook(Request $request){
        $validated = $request->validate([
                'book_name' => 'required|string',
                'description' => 'required|string',
                'category' => 'required|string',
                'book_picture' => ' required|string',
        ]);
        try{

            if ($request->hasFile()){
                $name = $request->file('book_picture')->getClientOriginalName();
                $extension = $request->file('book_picture')->getClientOriginalExtension();
                $name_no_ext = pathinfo($name, PATHINFO_FILENAME);
                $name_to_store = $name_no_ext.'_.'.$extension;
                $img_to_store = $request->file('book_picture')->storeAs('public/book/'.$name_to_store);
            }else{
                //
            }

            $book = new Book;
            $book->book_name = $validated['book_name'];
            $book->description = $validated['description'];
            $book->category = $validated['category'];
            $book->book_picture = $name_to_store;
            $book->save();

            return $this->success($book,"Upload Book Successfull", 201);
        }catch(Exception $e){return $this->error($e->getMessage(), "Uploading books failed", 400);}

    }

    public function filterBooks($category){

        try{


            $books = Book::where('category', $category)->get();
            foreach($books as $book){
                $data = [
                    'book_name' => $book->book_name,
                    'description' => $book->description,
                    'category' => $book->category,
                    'book_picture' => $book->book_picture,

                ];
            }

            return $this->success($data,"Filter Books", 200);
        }catch(Exception $e){return $this->error($e->getMessage(), "Filtering books failed", 400);}

    }

    public function chat(){}
    public function EmailSubscription($id){}

}
