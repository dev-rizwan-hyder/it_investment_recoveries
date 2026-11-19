<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Store\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $categoriesQuery = Category::whereNull('parent_id')
            ->with('children.children')
            ->orderBy('name');

        if ($search !== '') {
            $categoriesQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhereHas('children', function ($childQuery) use ($search) {
                        $childQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhereHas('children', function ($subChildQuery) use ($search) {
                                $subChildQuery->where('name', 'like', '%' . $search . '%');
                            });
                    });
            });
        }

        $categories = $categoriesQuery->paginate(5)->withQueryString();

        $allCategories = Category::orderBy('name')->get();

        return view('admin.categories.index', compact('categories', 'allCategories', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        $parentCatVal = $request->input('parent_category');
        $subCatVal = $request->input('sub_category');
        $subSubCatVal = $request->input('sub_sub_category');

        $ensureCategory = function ($val, $parentId = null) {
            if (empty($val)) {
                return null;
            }

            if (is_numeric($val)) {
                return (int) $val;
            }

            if (preg_match('/^new:(.*)$/', (string) $val, $m)) {
                $name = trim($m[1]);

                $existing = Category::where('name', $name)
                    ->when($parentId !== null, function ($q) use ($parentId) {
                        $q->where('parent_id', $parentId);
                    }, function ($q) {
                        $q->whereNull('parent_id');
                    })->first();

                if ($existing) {
                    return $existing->id;
                }

                $slugBase = Str::slug($name);
                $slug = $slugBase;
                $counter = 1;
                while (Category::where('slug', $slug)->exists()) {
                    $slug = $slugBase . '-' . $counter;
                    $counter++;
                }

                $cat = Category::create([
                    'name' => $name,
                    'parent_id' => $parentId,
                    'slug' => $slug,
                ]);

                return $cat->id;
            }

            // If it's a plain string (not starting with new:), try to find an existing category by id or name
            if (is_string($val) && is_numeric($val)) {
                return (int) $val;
            }

            $found = Category::where('name', (string) $val)
                ->when($parentId !== null, function ($q) use ($parentId) {
                    $q->where('parent_id', $parentId);
                }, function ($q) {
                    $q->whereNull('parent_id');
                })->first();

            return $found ? $found->id : null;
        };

        // Create/resolve parent chain if any 'new:' tags were used
        $parentId = $ensureCategory($parentCatVal, null);
        $subId = $ensureCategory($subCatVal, $parentId);
        $subSubId = $ensureCategory($subSubCatVal, $subId);

        // Decide where to attach a manually entered name, if present
        if (!empty($validated['name'])) {
            $nameToCreate = $validated['name'];
            // prefer deepest selected category as parent
            $attachParent = $subSubId ?? $subId ?? $parentId ?? ($validated['parent_id'] ?? null);

            $exists = Category::where('name', $nameToCreate)
                ->when($attachParent !== null, function ($q) use ($attachParent) {
                    $q->where('parent_id', $attachParent);
                }, function ($q) {
                    $q->whereNull('parent_id');
                })->exists();

            if ($exists) {
                return redirect()
                    ->route('admin.categories.index')
                    ->withInput()
                    ->with('error', 'A category with this name already exists at this level.');
            }

            $slugBase = Str::slug($nameToCreate);
            $slug = $slugBase;
            $counter = 1;
            while (Category::where('slug', $slug)->exists()) {
                $slug = $slugBase . '-' . $counter;
                $counter++;
            }

            Category::create([
                'name' => $nameToCreate,
                'parent_id' => $attachParent,
                'slug' => $slug,
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Category created successfully.']);
            }

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category created successfully.');
        }

        // If no manual name, but one of the selects used a 'new:' tag then ensureCategory already created it.
        if (preg_match('/^new:/', (string) $subSubCatVal) && $subSubId) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Category created successfully.']);
            }

            return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
        }

        if (preg_match('/^new:/', (string) $subCatVal) && $subId) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Category created successfully.']);
            }

            return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
        }

        if (preg_match('/^new:/', (string) $parentCatVal) && $parentId) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Category created successfully.']);
            }

            return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
        }

        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Please provide a category name or create one using the dropdowns.'], 422);
        }

        return redirect()
            ->route('admin.categories.index')
            ->withInput()
            ->with('error', 'Please provide a category name or create one using the dropdowns.');
    }

    public function destroy(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        if ($category->children()->exists()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete category that has subcategories. Please remove or reassign its children first.'], 422);
            }

            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Cannot delete category that has subcategories. Please remove or reassign its children first.');
        }

        $category->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Category deleted successfully.']);
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
