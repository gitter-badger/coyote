<?php

namespace Coyote\Http\Controllers\Forum;

use Coyote\Services\Elasticsearch\Builders\Forum\SearchBuilder;
use Coyote\Repositories\Contracts\UserRepositoryInterface as UserRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends BaseController
{
    const FIELD_IP          = 'ip';
    const FIELD_USER        = 'user';
    const FIELD_BROWSER     = 'browser';
    const FIELD_HOST        = 'host';

    /**
     * @param Request $request
     * @param UserRepository $user
     * @return mixed
     */
    public function index(Request $request, UserRepository $user)
    {
        $this->breadcrumb->push('Szukaj', route('forum.search'));

        // create forum list for current user (according to user's privileges)
        $this->pushForumCriteria();
        $forumList = $this->forum->choices('id'); // forum id as key

        $users = [];
        $response = $highlights = null;

        if ($request->has('q')) {
            $forumsId = array_keys($forumList);
            // we have to make sure user is not trying to search in category without access
            $this->validate($request, ['f' => 'sometimes|int|in:' . implode(',', $forumsId)]);

            // we need to limit results to given categories...
            $builder = (new SearchBuilder())->build($request, $request->has('f') ? $request->get('f') : $forumsId);

            $build = $builder->build();
            debugbar()->debug(json_encode($build));

            start_measure('Elasticsearch');
            $response = $this->post->search($build);
            stop_measure('Elasticsearch');

            $highlights = $response->getHighlights();

            if ($response->total() > 0) {
                $usersId = $response->keyBy('_source.user_id')->keys();
                $users = $user->whereIn('id', array_map('intval', $usersId->toArray()))->get()->keyBy('id');
            }

            $this->breadcrumb->push('Wyniki wyszukiwania', $request->fullUrl());

            $pagination = new LengthAwarePaginator($response, $response->total(), 10, null, ['path' => ' ']);
            $pagination->appends($request->except('page'));
        }

        return $this->view('forum.search')->with(compact('forumList', 'users', 'response', 'highlights', 'pagination'));
    }
}
