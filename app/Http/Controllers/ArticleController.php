<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ArticleController extends Controller implements HasMiddleware
{

    public static function middleware():array
    {
        return [
            new Middleware('permission:view-articles', only: ['index']), 
            new Middleware('permission:edit-articles', only: ['edit']), 
            new Middleware('permission:create-articles', only: ['create']), 
            new Middleware('permission:delete-articles', only: ['destroy']), 
        ];
    }
    public function index()
    {
        $articles = Article::paginate(10);
        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        return view('articles.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|max:255',
            'content' => 'required|min:10',
            'author' => 'required'
        ]);

        if ($validator->passes()) {
            Article::create([
                'title' => $request->title,
                'content' => $request->content,
                'author' => $request->author,
            ]);
            return redirect()->route('articles.index')->with('success', 'Article created successfully.');
        } else {
            return redirect()->route('articles.create')->withInput()->withErrors($validator);
        }
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);
        return view('articles.show', compact('article'));
    }

    public function edit($id)
    {
        $article = Article::findOrFail($id);
        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'title'   => 'required|min:3|max:255',
            'content' => 'required|min:10',
            'author'  => 'required'
        ]);

        if ($validator->passes()) {
            $article->update([
                'title' => $request->title,
                'content' => $request->content,
                'author'  => $request->author
            ]);
            return redirect()->route('articles.index')->with('success', 'Article updated successfully.');
        } else {
            return redirect()->route('articles.edit', $id)->withInput()->withErrors($validator);
        }
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article deleted successfully.');
    }
}