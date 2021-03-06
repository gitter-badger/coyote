<?php

namespace Coyote\Http\Controllers\Forum;

use Coyote\Http\Factories\FlagFactory;
use Coyote\Repositories\Criteria\Topic\BelongsToForum;
use Coyote\Repositories\Criteria\Topic\StickyGoesFirst;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    use FlagFactory;

    /**
     * @param \Coyote\Forum $forum
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($forum, Request $request)
    {
        // builds breadcrumb for this category
        $this->breadcrumb($forum);

        $this->pushForumCriteria();
        $forumList = $this->forum->choices();

        $this->forum->skipCriteria(true);
        // execute query: get all subcategories that user can has access to
        $sections = $this->forum->groupBySections($this->userId, $this->sessionId, $forum->id);

        // display topics for this category
        $this->topic->pushCriteria(new BelongsToForum($forum->id));
        $this->topic->pushCriteria(new StickyGoesFirst());
        // get topics according to given criteria
        $topics = $this->topic->paginate(
            $this->userId,
            $this->sessionId,
            'topics.last_post_id',
            'DESC',
            $this->topicsPerPage($request)
        );

        // we need to get an information about flagged topics. that's how moderators can notice
        // that's something's wrong with posts.
        if ($topics->total() > 0 && $this->getGateFactory()->allows('delete', $forum)) {
            $flags = $this->getFlagFactory()->takeForTopics($topics->groupBy('id')->keys()->toArray());
        }

        $collapse = $this->collapse();
        $postsPerPage = $this->postsPerPage($this->getRouter()->getCurrentRequest());

        return $this->view('forum.category')->with(
            compact('forumList', 'forum', 'topics', 'sections', 'collapse', 'flags', 'postsPerPage')
        );
    }

    /**
     * @param $forum
     */
    public function mark($forum)
    {
        $this->forum->markAsRead($forum->id, $this->userId, $this->sessionId);
        $forums = $this->forum->where('parent_id', $forum->id)->get();

        foreach ($forums as $forum) {
            $this->forum->markAsRead($forum->id, $this->userId, $this->sessionId);
        }
    }

    /**
     * Set category visibility
     *
     * @param \Coyote\Forum $forum
     * @param Request $request
     */
    public function section($forum, Request $request)
    {
        $collapse = $this->getSetting('forum.collapse');
        if ($collapse !== null) {
            $collapse = unserialize($collapse);
        }

        $collapse[$forum->id] = (int) $request->input('flag');
        $this->setSetting('forum.collapse', serialize($collapse));
    }
}
