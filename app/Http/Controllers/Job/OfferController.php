<?php

namespace Coyote\Http\Controllers\Job;

use Carbon\Carbon;
use Coyote\Http\Controllers\Controller;
use Coyote\Http\Factories\FlagFactory;
use Coyote\Repositories\Contracts\FirmRepositoryInterface as FirmRepository;
use Coyote\Repositories\Contracts\JobRepositoryInterface as JobRepository;
use Coyote\Firm;
use Coyote\Job;
use Coyote\Services\Elasticsearch\Builders\Job\MoreLikeThisBuilder;

class OfferController extends Controller
{
    use FlagFactory;

    /**
     * @var JobRepository
     */
    private $job;

    /**
     * @var FirmRepository
     */
    private $firm;

    /**
     * OfferController constructor.
     * @param JobRepository $job
     * @param FirmRepository $firm
     */
    public function __construct(JobRepository $job, FirmRepository $firm)
    {
        parent::__construct();

        $this->job = $job;
        $this->firm = $firm;
    }

    /**
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        // call method from repository to fetch job data and country name and currency
        $job = $this->job->findById($id);

        $this->breadcrumb->push('Praca', route('job.home'));
        $this->breadcrumb->push($job->title, route('job.offer', [$job->id, $job->slug]));

        $parser = app('parser.job');

        foreach (['description', 'requirements', 'recruitment'] as $name) {
            if (!empty($job->$name)) {
                $job->$name = $parser->parse($job->$name);
            }
        }

        $tags = $job->tags()->get()->groupBy('pivot.priority');

        $firm = [];
        if ($job->firm_id) {
            $firm = $this->firm->find($job->firm_id);
            $firm->description = $parser->parse($firm->description);
        }

        $job->increment('views');
        $previous = url()->previous();

        if ($previous && mb_strlen($previous) < 200) {
            $referer = $job->referers()->firstOrNew(['url' => $previous]);

            if (!$referer->id) {
                $referer->save();
            } else {
                $referer->increment('count');
            }
        }

        if ($this->getGateFactory()->allows('job-delete')) {
            $flag = $this->getFlagFactory()->takeForJob($job->id);
        }

        $builder = (new MoreLikeThisBuilder())->build($job);

        $build = $builder->build();
        debugbar()->debug(json_encode($build));

        debugbar()->startMeasure('More like this');
        // search related topics
        $mlt = $this->job->search($build)->getSource();
        debugbar()->stopMeasure('More like this');

        return $this->view('job.offer', [
            'ratesList'         => Job::getRatesList(),
            'employmentList'    => Job::getEmploymentList(),
            'employeesList'     => Firm::getEmployeesList(),
            'deadline'          => Carbon::parse($job->deadline_at)->diff(Carbon::now())->days,
            'subscribed'        => $this->userId ? $job->subscribers()->forUser($this->userId)->exists() : false
        ])->with(
            compact('job', 'firm', 'tags', 'flag', 'mlt')
        );
    }
}
