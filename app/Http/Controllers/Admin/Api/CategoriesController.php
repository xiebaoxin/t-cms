<?php

namespace App\Http\Controllers\Admin\Api;


use App\Entities\Category;
use App\Http\Requests\CategoryCreateRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Transformers\CategoryTransformer;
use App\Transformers\PostTransformer;
use Illuminate\Http\Request;

class CategoriesController extends ApiController
{
    /**
     * 获取导航栏
     * @return array
     */
    public function nav()
    {
        return Category::getNav();
    }

    /**
     * 创建分类
     * @param CategoryCreateRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function store(CategoryCreateRequest $request)
    {
        Category::create($request->all());
        return $this->response->noContent();
    }

    /**
     * 更新权限
     * @param Category $category
     * @param CategoryUpdateRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function update(Category $category, CategoryUpdateRequest $request)
    {
        $request->performUpdate($category);
        return $this->response->noContent();
    }

    /**
     * 获取一级分类
     * @return \Dingo\Api\Http\Response
     */
    public function getTopCategories()
    {
        $topCategories = Category::topCategories()
            ->withSimpleSearch()
            ->ordered()
            ->recent()
            ->get();
        return $this->response->collection($topCategories, new CategoryTransformer())
            ->setMeta(Category::getAllowSearchFieldsMeta());

    }

    /**
     * 获取子级分类
     * @param Category $category
     * @return \Dingo\Api\Http\Response
     */
    public function getChildren(Category $category)
    {
        $childrenCategories = Category::childrenCategories($category->id)
            ->withSimpleSearch()
            ->ordered()
            ->recent()
            ->get();
        return $this->response->collection($childrenCategories, new CategoryTransformer())
            ->setMeta(Category::getAllowSearchFieldsMeta());
    }

    /**
     * 获取指定分类下的文章
     * @param Category $category
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function posts(Category $category, Request $request)
    {
        $posts = $category->posts()
            ->applyFilter($request)
            ->with('user')
            ->with('categories')
            ->paginate($this->perPage());
        return $this->response->paginator($posts, new PostTransformer());
    }

}