<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogContent;
use App\Models\BlogContentImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BlogController extends Controller
{
    public function index()
    {
        return view('admin.blogs.index');
    }

    public function getList()
    {
        $blogs = Blog::withCount('contents');

        return DataTables::of($blogs)
            ->addIndexColumn()
            ->editColumn('thumbnail', function ($blog) {
                if ($blog->thumbnail) {
                    $url = asset('storage/blogs/' . $blog->thumbnail);
                    return '<img src="' . $url . '" alt="Thumbnail" class="img-thumbnail" style="width:60px;height:60px;object-fit:cover;">';
                }
                return '<span class="text-muted">No image</span>';
            })
            ->addColumn('sections', function ($blog) {
                return '<span class="badge bg-info-subtle text-info">' . $blog->contents_count . ' sections</span>';
            })
            ->addColumn('action', function ($blog) {
                $showUrl = route('admin.blogs.show', $blog->id);
                $editUrl = route('admin.blogs.edit', $blog->id);
                return '<div class="d-flex gap-2">' .
                    '<a href="' . $showUrl . '" class="btn btn-sm btn-outline-primary px-3" title="View"><i class="ti ti-eye"></i></a>' .
                    '<a href="' . $editUrl . '" class="btn btn-sm btn-primary px-3" title="Edit"><i class="ti ti-edit"></i></a>' .
                    '<a href="#" class="btn btn-sm btn-danger px-3 delete-btn" data-id="' . $blog->id . '" title="Delete"><i class="ti ti-trash"></i></a>' .
                    '</div>';
            })
            ->rawColumns(['thumbnail', 'sections', 'action'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'              => 'required|string|max:255',
            'main_image_data'    => 'nullable|string',
            'section_titles'     => 'required|array|min:1',
            'section_titles.*'   => 'required|string|max:255',
            'section_contents'   => 'required|array|min:1',
            'section_contents.*' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Handle thumbnail
            $thumbnailName = null;
            if ($request->filled('main_image_data')) {
                $thumbnailName = $this->saveBase64Image($request->main_image_data, 'blogs');
            }

            $blog = Blog::create([
                'title'     => $request->title,
                'thumbnail' => $thumbnailName,
            ]);

            // Save sections
            $titles   = $request->section_titles ?? [];
            $contents = $request->section_contents ?? [];

            foreach ($titles as $i => $heading) {
                $content = BlogContent::create([
                    'blog_id' => $blog->id,
                    'heading' => $heading,
                    'content' => $contents[$i] ?? '',
                    'order'   => $i,
                ]);

                // Section images
                $sectionImagesData = $request->input('section_images_data');
                if (isset($sectionImagesData[$i]) && is_array($sectionImagesData[$i])) {
                    foreach ($sectionImagesData[$i] as $imgData) {
                        $imgName = $this->saveBase64Image($imgData, 'blog_sections');
                        if ($imgName) {
                            BlogContentImage::create([
                                'blog_content_id' => $content->id,
                                'image_path'      => 'blog_sections/' . $imgName,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.blogs.index')->with('success', 'Blog created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create blog: ' . $e->getMessage());
        }
    }

    public function show(Blog $blog)
    {
        $contents = $blog->contents()->with('images')->get();
        return view('admin.blogs.show', compact('blog', 'contents'));
    }

    public function edit(Blog $blog)
    {
        $contents = $blog->contents()->with('images')->get();
        return view('admin.blogs.edit', compact('blog', 'contents'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title'              => 'required|string|max:255',
            'main_image_data'    => 'nullable|string',
            'section_titles'     => 'required|array|min:1',
            'section_titles.*'   => 'required|string|max:255',
            'section_contents'   => 'required|array|min:1',
            'section_contents.*' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Handle thumbnail update
            $thumbnailName = $blog->thumbnail;
            if ($request->filled('main_image_data')) {
                // Delete old thumbnail
                if ($blog->thumbnail) {
                    Storage::disk('public')->delete('blogs/' . $blog->thumbnail);
                }
                $thumbnailName = $this->saveBase64Image($request->main_image_data, 'blogs');
            }

            $blog->update([
                'title'     => $request->title,
                'thumbnail' => $thumbnailName,
            ]);

            // Handle removed sections
            $removedSections = $request->input('removed_sections', []);
            if (!empty($removedSections)) {
                $sectionsToRemove = BlogContent::whereIn('id', $removedSections)->with('images')->get();
                foreach ($sectionsToRemove as $sec) {
                    foreach ($sec->images as $img) {
                        Storage::disk('public')->delete($img->image_path);
                    }
                    $sec->delete();
                }
            }

            // Handle removed images per section
            $removedImages = $request->input('removed_images', []);
            foreach ($removedImages as $contentId => $imageIds) {
                foreach ((array)$imageIds as $imageId) {
                    $img = BlogContentImage::find($imageId);
                    if ($img) {
                        Storage::disk('public')->delete($img->image_path);
                        $img->delete();
                    }
                }
            }

            // Update / create sections
            $titles      = $request->section_titles ?? [];
            $contentTexts = $request->section_contents ?? [];
            $sectionIds  = $request->input('section_ids', []);

            foreach ($titles as $i => $heading) {
                $sectionId = $sectionIds[$i] ?? null;

                if ($sectionId) {
                    $section = BlogContent::find($sectionId);
                    if ($section) {
                        $section->update([
                            'heading' => $heading,
                            'content' => $contentTexts[$i] ?? '',
                            'order'   => $i,
                        ]);
                    }
                } else {
                    $section = BlogContent::create([
                        'blog_id' => $blog->id,
                        'heading' => $heading,
                        'content' => $contentTexts[$i] ?? '',
                        'order'   => $i,
                    ]);
                }

                // New section images
                $sectionImagesData = $request->input('section_images_data');
                if (isset($sectionImagesData[$i]) && is_array($sectionImagesData[$i])) {
                    foreach ($sectionImagesData[$i] as $imgData) {
                        $imgName = $this->saveBase64Image($imgData, 'blog_sections');
                        if ($imgName) {
                            BlogContentImage::create([
                                'blog_content_id' => $section->id,
                                'image_path'      => 'blog_sections/' . $imgName,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.blogs.index')->with('success', 'Blog updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update blog: ' . $e->getMessage());
        }
    }

    public function destroy(Blog $blog)
    {
        try {
            // Delete thumbnail
            if ($blog->thumbnail) {
                Storage::disk('public')->delete('blogs/' . $blog->thumbnail);
            }

            // Delete section images
            foreach ($blog->contents()->with('images')->get() as $content) {
                foreach ($content->images as $img) {
                    Storage::disk('public')->delete($img->image_path);
                }
            }

            $blog->delete();

            return response()->json(['status' => true, 'message' => 'Blog deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to delete blog: ' . $e->getMessage()], 500);
        }
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    private function saveBase64Image(string $dataUrl, string $folder): ?string
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $matches)) {
            return null;
        }
        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $data      = preg_replace('/^data:image\/\w+;base64,/', '', $dataUrl);
        $data      = str_replace(' ', '+', $data);
        $decoded   = base64_decode($data);
        $filename  = uniqid('blog_') . '.' . $extension;

        Storage::disk('public')->put($folder . '/' . $filename, $decoded);

        return $filename;
    }
}
