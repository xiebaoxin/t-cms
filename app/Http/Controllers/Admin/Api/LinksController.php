<?php

/**
 * 友情链接
 */

namespace App\Http\Controllers\Admin\Api;


use App\Entities\Link;
use App\Entities\Type;
use App\Http\Requests\LinkCreateRequest;
use App\Http\Requests\LinkUpdateRequest;
use App\Transformers\LinkTransformer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LinksController extends ApiController
{

    public function lists(Type $type = null)
    {
        $links = Link::byType($type)
            ->withSimpleSearch()
            ->withSort()
            ->paginate();
        return $this->response->paginator($links, new LinkTransformer())
            ->setMeta(Link::getAllowSortFieldsMeta() + Link::getAllowSearchFieldsMeta());
    }

    public function store(LinkCreateRequest $request)
    {
        Link::create($request->all());
        return $this->response->noContent();
    }

    public function update(Link $link, LinkUpdateRequest $request)
    {
        $request->performUpdate($link);
        return $this->response->noContent();
    }

    public function destroy($id)
    {
        if (!Link::destroy(intval($id))) {
            throw new NotFoundHttpException(trans('message.link_not_found'));
        }
        return $this->response->noContent();
    }
}