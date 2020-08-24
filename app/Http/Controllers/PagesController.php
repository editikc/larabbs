<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Auth;

class PagesController extends Controller
{
    public function index()
    {
        $feed_items = Article::where([])->orderBy('created_at', 'desc')->get();
        $user=Auth::user();
        return view('article.index', compact('feed_items','user'));
    }

    public function permissionDenied()
    {
        // 如果当前用户有权限访问后台，直接跳转访问
        if (config('administrator.permission')()) {
            return redirect(url(config('administrator.uri')), 302);
        }
        // 否则使用视图
        return view('layouts.permission_denied');
    }
}
