<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormPostRequest;
use App\Models\Post;
use App\Http\Requests\BlogFilterRequest;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category; // Importation de la classe Category
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Tag;
use Illuminate\View\View;
use App\Http\Requests\CreatePostRequest;


class BlogController extends Controller





{
    public function create()
    {
        $post = new Post();
        $post->title = 'Bojour';

        return view('blog.create',[
            'post' => $post,
            'categories' => Category::select('id', 'name')->get(),
            'tags' => Tag::select('id', 'name')->get()
        ]);
    }

    public function store(CreatePostRequest $request) {

        $post = Post::create($request->validated());

        $post->tags()->sync($request->validated('tags'));

        return redirect()->route('blog.show', ['slug' => $post->slug, 'post' => $post->id])->with('success', "L'article a bien été sauvegardé");
    }



    public function edit(Post $post)
    {
        return view('blog.edit',[
            'post' => $post,
            'categories' => Category::select('id', 'name')->get(),
            'tags' => Tag::select('id', 'name')->get()
        ]);
    }

    public function update(Post $post, FormPostRequest $request)
    {
        $date = $request->validated();
        /** @var UploadedFile|null $image*/
        $image = $request->validated('image');
        if ($image != null && !$image->getError()) {
            $imagedate['image'] = $image->store('blog', 'public');
        }
        $post->update($date);
        $post->update($request->validated());
        $post->tags()->sync($request->validated('tags'));
        return redirect()->route('blog.show', ['slug' => $post->slug, 'post' => $post->id])->with('success',
        "L'article a bien été modifier");
    }




    public function index(): View {
       /* User::create([
            'name' => 'Adjaye',
            'email' => 'adjaye86@gmail.com',
            'password' => Hash::make('0000')
        ]);*/
        return view('blog.index', [
            'posts' => Post::with('tags', 'category')->paginate(10) // Modification de la pagination
        ]);
    }



    public function show(string $slug, int $post): RedirectResponse | View // Modification du type du paramètre $post
    {
        $post = Post::findOrFail($post); // Récupération du post par son ID
        if ($post->slug != $slug) {
            return redirect()->route('blog.show', ['slug' => $post->slug, 'post' => $post->id]);
        }

        return view('blog.show', [
            'post' => $post
        ]);
    }
}
