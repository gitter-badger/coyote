<?php

namespace Coyote\Http\Controllers\Forum;

use Coyote\Services\Stream\Activities\Vote as Stream_Vote;
use Coyote\Services\Stream\Objects\Topic as Stream_Topic;
use Coyote\Services\Stream\Objects\Post as Stream_Post;
use Coyote\Services\UrlBuilder\UrlBuilder;

class VoteController extends BaseController
{
    /**
     * @param \Coyote\Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($post)
    {
        if (auth()->guest()) {
            return response()->json(['error' => 'Musisz być zalogowany, aby oddać ten głos.'], 500);
        }

        if (!config('app.debug') && auth()->user()->id === $post->user_id) {
            return response()->json(['error' => 'Nie możesz głosować na wpisy swojego autorstwa.'], 500);
        }

        $topic = $post->topic;
        if ($topic->is_locked) {
            return response()->json(['error' => 'Wątek jest zablokowany.'], 500);
        }

        $forum = $topic->forum;
        if ($forum->is_locked) {
            return response()->json(['error' => 'Forum jest zablokowane.'], 500);
        }

        $this->transaction(function () use ($post, $topic, $forum) {
            $vote = $post->votes()->forUser($this->userId)->first();

            // build url to post
            $url = UrlBuilder::post($post);
            // excerpt of post text. used in reputation and alert
            $excerpt = excerpt($post->html);

            if ($vote) {
                $vote->delete();
                $post->score--;
            } else {
                $post->votes()->create([
                    'user_id' => $this->userId, 'forum_id' => $forum->id, 'ip' => request()->ip()
                ]);
                $post->score++;

                // send notification to the user
                app('alert.post.vote')
                    ->setPostId($post->id)
                    ->setUsersId($forum->onlyUsersWithAccess([$post->user_id]))
                    ->setSubject(str_limit($topic->subject, 84))
                    ->setExcerpt($excerpt)
                    ->setSenderId($this->userId)
                    ->setSenderName(auth()->user()->name)
                    ->setUrl($url)
                    ->notify();
            }

            // increase/decrease reputation points according to the forum settings
            if ($post->user_id && $forum->enable_reputation) {
                // add or subtract reputation points
                app('reputation.post.vote')
                    ->setUserId($post->user_id)
                    ->setPositive(!count($vote))
                    ->setUrl($url)
                    ->setPostId($post->id)
                    ->setExcerpt($excerpt)
                    ->save();
            }

            // add into activity stream
            stream(Stream_Vote::class, (new Stream_Post(['url' => $url]))->map($post), (new Stream_Topic())->map($topic));
        });

        return response()->json(['count' => $post->score]);
    }
}
