<?php

namespace App\Store\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = ['name', 'parent_id', 'slug'];

    public $timestamps = false;

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getHierarchy(): array
    {
        $category = $this;
        $subCategory = null;
        $subSubCategory = null;

        if ($category->parent) {
            $subCategory = $category;
            $category = $category->parent;

            if ($category->parent) {
                $subSubCategory = $subCategory;
                $subCategory = $category;
                $category = $category->parent;
            }
        }

        return [
            'category' => $category->name ?? '',
            'sub_category' => $subCategory->name ?? '',
            'sub_sub_category' => $subSubCategory->name ?? '',
            'category_id' => $category->id ?? null,
            'sub_category_id' => $subCategory->id ?? null,
            'sub_sub_category_id' => $subSubCategory->id ?? null,
        ];
    }
}