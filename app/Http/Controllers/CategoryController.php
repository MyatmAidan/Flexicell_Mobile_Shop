<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\CategoryCreateRequest;
use App\Http\Requests\CategoryUpdateRequest;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.category.index');
    }

    public function store(CategoryCreateRequest $request)
    {
        $data = $request->validated();

        Category::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Category created successfully'
        ], 201);
    }


    public function update(CategoryUpdateRequest $request, $id)
    {
        $category = Category::findOrFail($id);

        $category->update([
            'category_name' => $request->category_name,
            'color'         => $request->color,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Category updated successfully'
        ]);
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return response()->json([
            'category' => $category
        ]);
    }


    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);;
    }

    public function getList()
    {
        $categories = Category::withCount('products');

        return DataTables::of($categories)
            ->addColumn('plus-icon', fn() => null)
            ->editColumn('color', function ($c) {
                $color = $c->color ?? '#000000';
                return '<div class="mx-auto" style="display:inline-block; width: 25px; height: 25px; background-color: ' . htmlspecialchars($color, ENT_QUOTES) . '; border-radius: 50%; border: 1px solid #ddd;" title="' . htmlspecialchars($color, ENT_QUOTES) . '"></div>';
            })
            ->addColumn('products_count', function ($c) {
                return '<span class="badge bg-info">' . $c->products_count . '</span>';
            })
            ->editColumn('created_at', function ($c) {
                return $c->created_at
                    ? $c->created_at->format('Y-m-d')
                    : '-';
            })
            ->addColumn('action', function ($category) {
                $id = $category->id;
                $editBtn = '<a href="#" class="btn btn-sm mx-2 px-3 edit-category-btn py-2 btn-primary" title="edit" data-id="' . $id . '" data-name="' . htmlspecialchars($category->category_name, ENT_QUOTES) . '" data-color="' . htmlspecialchars($category->color ?? '#000000', ENT_QUOTES) . '"><i class="fas fa-edit"></i> </a>';
                $deleteBtn = '<a href="#" class="btn btn-danger btn-sm px-3 py-2 delete-btn" data-id="' . $id . '" title="delete"><i class="fa fa-trash-alt"></i> </a>';
                return '<div class="action-btn" role="group">' . $editBtn . ' ' . $deleteBtn . '</div>';
            })
            ->rawColumns(['action', 'color', 'products_count'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.category.create');
    }
}
