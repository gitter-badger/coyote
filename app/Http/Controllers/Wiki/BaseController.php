<?php

namespace Coyote\Http\Controllers\Wiki;

use Coyote\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Coyote\Repositories\Contracts\WikiRepositoryInterface as WikiRepository;

abstract class BaseController extends Controller
{
    /**
     * @var WikiRepository
     */
    protected $wiki;

    /**
     * @param Request $request
     * @param WikiRepository $wiki
     */
    public function __construct(Request $request, WikiRepository $wiki)
    {
        parent::__construct();

        $this->wiki = $wiki;
        $this->buildBreadcrumb($request->wiki);
    }

    /**
     * @param \Coyote\Wiki $wiki
     */
    protected function buildBreadcrumb($wiki)
    {
        $this->wiki->parents($wiki->id)->reverse()->each(function ($item) {
            /** @var \Coyote\Wiki $item */
            $this->breadcrumb->push($item->title, url($item->path));
        });
    }
}