<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use Auth;

class ArticlesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    // 创建文章页面
    public function create(Article $article)
    {
        $categories = Category::all();
        return view('article.create', compact('article', 'categories'));
    }

    // 创建文章方法
    public function store(Request $request, Article $article)
    {
        $this->validate($request, [
            'title' => 'required|min:3|max:30',
            'content' => 'required|min:3',
        ]);

        $article->fill($request->all());
        $article->user_id = Auth::id();
        $article->save();

        return redirect()->route('index', $article->id)->with('success', '文章创建成功！');
    }

    // 创建文章图片上传
    public function uploadImage(Request $request, ImageUploadHandler $uploader)
    {
        $data = [
            'success' => false,
            'msg'    => '上传失败!',
            'file_path' => ''
        ];
        if ($file = $request->upload_file) {
            $result = $uploader->save($file, 'article', \Auth::id(), 1024);
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg'] = "上传成功！";
                $data['success'] = true;
            }
        }
        return $data;
    }

    // 编辑文章页面
    public function edit(Article $article)
    {
        $categories = Category::all();
        return view('article.edit', compact('article','categories'));
    }

    // 编辑文章方法
    public function update(Article $article, Request $request)
    {
        $article->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);
        session()->flash('success', '文章更新成功！');
        return redirect()->route('users.show', $article->id);
    }

    // 删除文章
    public function destroy(Article $article)
    {
        // $this->authorize('destroy', $article);
        Article::destroy($article->id);
        session()->flash('success', '文章已被成功删除！');
        return redirect()->back();
    }

    // 文章详情显示
    public function show(Article $article)
    {
        $user=Auth::user();
        return view('article.show', compact('article','user'));
    }

}
