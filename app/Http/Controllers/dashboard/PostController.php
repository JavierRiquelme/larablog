<?php

namespace App\Http\Controllers\dashboard;

use App\Tag;
use App\Post;
use App\Category;
use App\PostImage;
use App\Helpers\CustomUrl;
use App\Exports\PostsExport;
use App\Imports\PostsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostPost;
use App\Http\Requests\UpdatePostPut;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class PostController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'rol.admin']);
    }

    public function export(){
        return Excel::download(new PostsExport, 'posts.xlsx');
    }

    public function import(){

        Excel::import(new PostsImport, 'posts.xlsx');

        return "Importado!";
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$this->sendMail();
        
        $posts = Post::with('category')->orderBy('created_at', request('created_at', 'DESC'));

        if ($request->has('search')) {
            $posts = $posts->where('title', 'like', '%' . request('search') . '%');
        }

        $posts = $posts->paginate(10);
     
        return view('dashboard.post.index', ['posts' => $posts]);
    }

    private function sendMail(){

        $data['title'] = "Datos de prueba";

        Mail::send('emails.email', $data, function ($message) {
            $message->to('javier@gmail.com', 'Javier')
                ->subject("Email de prueba larablog");
        });

        if (Mail::failures()) {
            return "Mensaje no enviado";
        } else {
            return "Mensaje enviado";
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tags = Tag::pluck('id','title');
        $categories = Category::pluck('id', 'title');
        $post = new Post();

        return view('dashboard/post/create', compact('post', 'categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostPost $request)
    {

        if ($request->url_clean == "") {
            $urlClean = CustomUrl::urlTitle(CustomUrl::convertAccentedCharacters($request->title),'-',true);
        }else{
            $urlClean = CustomUrl::urlTitle(CustomUrl::convertAccentedCharacters($request->url_clean),'-',true);
        }

        $requestData = $request->validated();
        $requestData['url_clean'] = $urlClean;

        $validator = Validator::make($requestData, StorePostPost::myRules());

        if($validator->fails()){
            return redirect('dashboard/post/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $post = Post::create($requestData);

        $post->tags()->sync($request->tags_id);

        return back()->with('status', 'Post creado con exito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {   
        return view('dashboard.post.show', ['post' => $post]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $tags = Tag::pluck('id','title');
        $categories = Category::pluck('id', 'title');

        return view('dashboard.post.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostPut $request, Post $post)
    {
        $post->tags()->sync($request->tags_id);

        $post->update($request->validated());

        return back()->with('status', 'Post actualizado con exito');
    }

    public function image(Request $request, Post $post){
        $request->validate([
            'image' => 'required|mimes:jpeg,bmp,png|max:10240'//10Mb
        ]);

        $filename = time().".".$request->image->extension();

        $path = $request->image->store('public/images');

        PostImage::create(['image' => $path, 'post_id' => $post->id]);
        return back()->with('status', 'Imagen cargada con exito');
    }

    public function contentImage(Request $request){
        $request->validate([
            'image' => 'required|mimes:jpeg,bmp,png|max:10240'//10Mb
        ]);

        $filename = time().".".$request->image->extension();

        $request->image->move(public_path('images_post'), $filename);

        return response()->json(["default" => URL::to('/').'/images_post/'.$filename]);
    }

    public function imageDownload(PostImage $image){
        return Storage::disk('local')->download($image->image);
    }

    public function imageDelete(PostImage $image){
        $image->delete();
        Storage::disk('local')->delete($image->image);

        return back()->with('status', 'Imagen eliminado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return back()->with('status', 'Post eliminado con exito');     
    }
}
