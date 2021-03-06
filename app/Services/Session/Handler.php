<?php

namespace Coyote\Services\Session;

use Illuminate\Database\Query\Expression;
use Illuminate\Session\DatabaseSessionHandler;
use Jenssegers\Agent\Agent;

class Handler extends DatabaseSessionHandler
{
    /**
     * {@inheritDoc}
     */
    public function write($sessionId, $data)
    {
        $request = $this->container->make('request');
        $auth    = $this->container->make('auth');

        $userId  = $auth->check() ? $auth->user()->id : null;
        $url     = $request->fullUrl();
        $ip      = $request->ip();
        $browser = '';

        // nie korzystamy ze standardowej klasy Request. Testy funkcjonalne "nie widza"
        // metody browser() z klasy CostomRequest
        if (method_exists($request, 'browser')) {
            $browser = $request->browser();
        }

        $data = [
            'payload'       => base64_encode($data),
            'updated_at'    => new Expression('NOW()'),
            'user_id'       => $userId,
            'url'           => $url,
            'ip'            => $ip,
            'browser'       => $browser
        ];

        if ($this->exists) {
            $this->getQuery()->where('id', $sessionId)->update($data);
        } else {
            $agent = new Agent();
            $agent->setUserAgent($browser);

            if ($agent->isRobot()) {
                $data['robot'] = $agent->robot();
            }

            try {
                $this->getQuery()->insert(['id' => $sessionId] + $data);
            } catch (\PDOException $e) {
                // tutaj moze byc blad z zapisem sesji w przypadku zapytan ajax
                // @see https://github.com/laravel/framework/issues/9251
                // docelowo implementacja bedzie zastapiona na redis
            } catch (\Exception $e) {
                throw $e;
            }
        }

        $this->exists = true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($lifetime)
    {
        $this->getQuery()->where('updated_at', '<=', new Expression("NOW() - INTERVAL '$lifetime seconds'"))->delete();
    }
}
