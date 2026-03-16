<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;

class BlogFrontController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $blogs      = Blog::latest()->paginate(9);

        return view('blog', compact('categories', 'blogs'));
    }

    public function show(Blog $blog)
    {
        $categories = Category::all();
        $contents   = $blog->contents()->with('images')->get();

        return view('blogDetails', compact('categories', 'blog', 'contents'));
    }
}
