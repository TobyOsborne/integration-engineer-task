<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Connectors\MailerLite;
use App\Http\Requests\UpdateSubscriberRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    /**
     * Display a view of the subscribers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('subscribers')->with(['title'=>__('views.subscribers')]);
    }


    /**
     * Display a listing of the subscribers.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        try {
            // prepare the main query
            $data = [];
            $data['query'] = !empty($request->search['value']) ? $request->search['value'] : '';
            $data['limit'] = !empty($request->length) ? $request->length : Setting::getSubscribersPerPage();
            $data['offset'] = !empty($request->start) ? $request->start : 0;

            // create a cache key from the settings.
            $cache_key = 'subscribers_'.md5(json_encode($data));

            // if the cache key exists then return it.
            if (Cache::tags(['subscribers'])->has($cache_key)) {
                return Cache::tags(['subscribers'])->get($cache_key);
            }

            // get the mailerlite instance.
            $mailerLite = new MailerLite();

            /**
             * Get the total count.
             * It's a seperate request because, the /subscribers/count isn't accessible through the batch endpoint.
             * */
            $count = $mailerLite->getSubscriberCount();
            
            // get the subscribers for our query.
            $results = $mailerLite->getSubscribers($data);

            // prepare the output
            $return = [];

            $return['draw'] = empty($request->draw) ? 0 : intval($request->draw);
            $return['data'] = $results;
            $return['recordsTotal'] = empty($count['count']) ? 0 : intval($count['count']);

            if (!empty($data['query'])) {
                // /subscribers/search limit doesn't work, so we can use the results to count.
                $return['recordsFiltered'] = !empty($results) ? count($results) : 0;
            } else {
                $return['recordsFiltered'] = $return['recordsTotal'];
            }

            // store the result for 60 seconds.
            Cache::tags(['subscribers'])->put($cache_key, $return, 60);
            
            return $return;
        } catch (\Exception $e) {
            $code = !empty($e->getCode()) ? $e->getCode() : 400;
            return response()->json(['message'=>$e->getMessage()], $code);
        }
    }
    /**
     * Show the form for creating a new subscriber.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('subscribers-form')->with(['title'=>__('views.subscribersAdd'), 'method'=>'POST']);
    }

    /**
     * Store a newly created subscriber in storage.
     *
     * @param  \App\Http\Requests\UpdateSubscriberRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UpdateSubscriberRequest $request)
    {
        try {
            // prepare the data
            $data = $request->safe()->merge(['type'=>'active','resubscribe'=>1])
                        ->only(['name', 'fields','email','resubscribe','type']);
            
            // handle the request
            $mailerLite = new MailerLite();
            $subscriber = $mailerLite->createSubscriber($data);

            // store the subscriber for 60 seconds.
            Cache::tags(['subscribers'])->put($subscriber['email'], $subscriber, 60);

            // Return the response.
            return response()->json($subscriber);
        } catch (\Exception $e) {
            $code = !empty($e->getCode()) ? $e->getCode() : 400;
            return response()->json(["errors"=>['message'=>$e->getMessage()]], $code);
        }
    }

    /**
     * Show the form for editing the specified subscriber.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
             // if there no cache we need to get it from the databse.
            if (!Cache::tags(['subscribers'])->has($id)) {
                // get the subscriber details.
                $mailerLite = new MailerLite();
                $subscriber = $mailerLite->getSubscriber($id);
            } else {
                // otherwise use the cached version.
                return Cache::tags(['subscribers'])->get($id);
            }
            
            // set the country field in an easier place to access.
            if ($subscriber['fields']) {
                $countryKey = array_search('country', array_column($subscriber['fields'], 'key'));
                $subscriber['country'] = $countryKey !==false ? $subscriber['fields'][$countryKey]['value'] : "";
            }
            
            // display the view
            return view('subscribers-form')
                    ->with(['title'=>__('views.subscribersEdit'), 'subscriber'=>$subscriber]);
        } catch (\Exception $e) {
            // if the code is 404, then the user doesn't exist.
            if ($e->getCode()===404) {
                // The user doesn't exist, show the add form with a notice.
                return view('subscribers-form')
                ->with(['title'=>__('views.subscribersAdd'), 'method'=>'POST', "subscriber"=>['email'=>$id]])
                ->withErrors(['message'=>$e->getMessage()]);
            }
            $code = !empty($e->getCode()) ? $e->getCode() : 400;

            // otherwise abort a generic error page.
            abort($code);
        }
    }

    /**
     * Update the specified subscriber in storage.
     *
     * @param  \App\Http\Requests\UpdateSubscriberRequest  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSubscriberRequest $request, $id)
    {
        try {
            // prepare the data
            $data = $request->safe()->only(['name', 'fields']);

            // handle the request
            $mailerLite = new MailerLite();
            $subscriber = $mailerLite->updateSubscriber($id, $data);
            
            // cache the update subscriber
            Cache::tags(['subscribers'])->put($subscriber['email'], $subscriber, 60);

            // Return the response.
            return response()->json($subscriber);
        } catch (\Exception $e) {
            $code = !empty($e->getCode()) ? $e->getCode() : 400;
            return response()->json(["errors"=>['message'=>$e->getMessage()]], $code);
        }
    }

    /**
     * Remove the specified subscriber from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // get the mailerlite instance.
            $mailerLite = new MailerLite();

            // attempt to delete the subscriber.
            $response = $mailerLite->deleteSubscriber($id);

            // clear the subscribers cache.
            Cache::tags(['subscribers'])->flush();
            
            return $response;
        } catch (\Exception $e) {
            $code = !empty($e->getCode()) ? $e->getCode() : 400;
            return response()->json(['message'=>$e->getMessage()], $code);
        }
    }
}
