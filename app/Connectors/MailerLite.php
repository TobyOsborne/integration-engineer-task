<?php
namespace App\Connectors;

use App\Exceptions\MailerLiteKeyNotSet;
use App\Models\Setting;

use Illuminate\Support\Facades\Http;

class MailerLite
{
    
    /**
     * The url to interact with mailerlites API. it's the older URL, so we can support multi-link pagination.
     * And the /me endpoint.
     *
     * @var string $url
     */
    public string $url = 'https://api.mailerlite.com/api/v2';

    /**
     * The API key.
     *
     * @var string $key
     */
    private string $key;

    /**
     * Sets the API key for MailerLite and throws an error if the key is not set.
     *
     * @param string|boolean $key`
     */
    public function __construct(string $key = "")
    {
        // if the key is empty, get it from the database.
        $this->key = empty($key) ? Setting::getAPIKey() : $key;

        // if the key is still empty, throw and error, we can't do anything with the key.
        if (empty($this->key)) {
            throw new MailerLiteKeyNotSet(__('mailerlite.api_key_not_set'), 400);
        }
    }

    
    /**
     * This function returns an HTTP request object with JSON acceptance and a MailerLite API key
     * header.
     *
     * @return Illuminate\Support\Facades\Http.
     */
    private function getBaseRequest()
    {
        return Http::acceptJson()->withHeaders(['X-MailerLite-ApiKey'=>$this->key,'']);
    }

    
    /**
     * The function processes a response and throws an exception if it is not successful.
     *
     * @param  Illuminate\Http\Client\Response $response The response object returned by an HTTP request.
     *
     * @return JSON $response the json from the request response.
     *
     * @throws \Exception
     */
    private function processResponse($response)
    {
        if (!$response->successful()) {
            $code = $response->status();
            $message = $response->reason();

            $data = $response->json();
            
            if (!empty($data['error']['code'])) {
                $code = $data['error']['code'];
            }

            if (!empty($data['error']['message'])) {
                $message = $data['error']['message'];
            }

            // maybe translate the message. (we might have a nicer one).
            $translated = __("mailerlite.$message") !== "mailerlite.$message" ? __("mailerlite.$message") : $message;

            throw new \Exception($translated, $code);
        }

        return $response->json();
    }

    /**
     * This function retrieves the account information of the users MailerLite account.
     *
     * @return JSON The json response.
     */
    public function getAccount() : array
    {
        $response = $this->getBaseRequest()->get("{$this->url}/me");
        return $this->processResponse($response);
    }

    public function getSubscribers(array $params = []) : array
    {
        $endpoint = !empty($params['query']) ? '/search' : '';
        $response = $this->getBaseRequest()->get("{$this->url}/subscribers{$endpoint}", $params);
        return $this->processResponse($response);
    }

    public function getSubscriber(string $id) : array
    {
        $response = $this->getBaseRequest()->get("{$this->url}/subscribers/$id");
        return $this->processResponse($response);
    }

    public function getSubscriberCount() : array
    {
        $response = $this->getBaseRequest()->get("{$this->url}/subscribers/count");
        return $this->processResponse($response);
    }

    public function createSubscriber(array $params) : array
    {
        $response = $this->getBaseRequest()->post("{$this->url}/subscribers", $params);
        return $this->processResponse($response);
    }

    public function updateSubscriber(string $id, array $params) : array
    {
        $response = $this->getBaseRequest()->put("{$this->url}/subscribers/$id", $params);
        return $this->processResponse($response);
    }

    public function deleteSubscriber(string $id) : array
    {
        $response = $this->getBaseRequest()->delete("{$this->url}/subscribers/{$id}");
        $this->processResponse($response);

        // if it gets this far the subscriber has been deleted
        return ['message'=>__('mailerlite.deleted')];
    }
}
