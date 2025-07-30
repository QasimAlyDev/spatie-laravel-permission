<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ArticleController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        // $this->authorize('view-article');
        $articles = Article::with('user')->paginate(10);
        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        // $this->authorize('create-article');
        return view('articles.create');
    }

    public function store(Request $request)
    {
        // $this->authorize('create-article');
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|max:255',
            'content' => 'required|min:10',
        ]);

        if ($validator->passes()) {
            Article::create([
                'title' => $request->title,
                'content' => $request->content,
                'user_id' => Auth::id(),
            ]);
            return redirect()->route('articles.index')->with('success', 'Article created successfully.');
        } else {
            return redirect()->route('articles.create')->withInput()->withErrors($validator);
        }
    }

    public function show($id)
    {
        // $this->authorize('view-article');
        $article = Article::with('user')->findOrFail($id);
        return view('articles.show', compact('article'));
    }

    public function edit($id)
    {
        // $this->authorize('edit-article');
        $article = Article::findOrFail($id);
        if ($article->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, $id)
    {
        // $this->authorize('edit-article');
        $article = Article::findOrFail($id);
        if ($article->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|max:255',
            'content' => 'required|min:10',
        ]);

        if ($validator->passes()) {
            $article->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
            return redirect()->route('articles.index')->with('success', 'Article updated successfully.');
        } else {
            return redirect()->route('articles.edit', $id)->withInput()->withErrors($validator);
        }
    }

    public function destroy($id)
    {
        // $this->authorize('delete-article');
        $article = Article::findOrFail($id);
        if ($article->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article deleted successfully.');
    }
}