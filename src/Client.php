<?php

namespace Pishran\IpPanel;

use Exception;
use Pishran\IpPanel\Models\InboxMessage;
use Pishran\IpPanel\Models\Message;
use Pishran\IpPanel\Models\PaginationInfo;
use Pishran\IpPanel\Models\Pattern;
use Pishran\IpPanel\Models\Recipient;

class Client
{
    const ENDPOINT = 'https://rest.ippanel.com';

    /**
     * @var Http
     */
    protected $client;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var int
     */
    protected $timeout;

    public function __construct(string $apiKey, int $timeout = 30)
    {
        $this->apiKey = $apiKey;
        $this->timeout = $timeout;
        $this->client = new Http(self::ENDPOINT, $timeout, [
            'Authorization: AccessKey '.$this->apiKey,
        ]);
    }

    /**
     * @throws Exception
     */
    public function getCredit(): float
    {
        $response = $this->client->get('/v1/credit');

        if (! isset($response->data->credit)) {
            throw new Exception('Invalid response received.', 1);
        }

        return $response->data->credit;
    }

    /**
     * @param  string  $originator
     * @param  string[]  $recipients
     * @param  string  $message
     * @return int
     *
     * @throws Exception
     */
    public function sendMessage(string $originator, array $recipients, string $message): int
    {
        $response = $this->client->post('/v1/messages', [
            'originator' => $originator,
            'recipients' => $recipients,
            'message' => $message,
        ]);

        if (! isset($response->data->bulk_id)) {
            throw new Exception('Invalid response received.', 1);
        }

        return $response->data->bulk_id;
    }

    /**
     * @throws Exception
     */
    public function getMessage(int $bulkId): Message
    {
        $response = $this->client->get('/v1/messages/'.$bulkId);

        if (! isset($response->data->message)) {
            throw new Exception('Invalid response received.', 1);
        }

        $message = new Message;
        $message->fromJson($response->data->message);

        return $message;
    }

    /**
     * @param  int  $bulkId
     * @param  int  $page
     * @param  int  $limit
     * @return array<int, Recipient[]|PaginationInfo>
     *
     * @throws Exception
     */
    public function fetchStatuses(int $bulkId, int $page = 0, int $limit = 10): array
    {
        $response = $this->client->get('/v1/messages/'.$bulkId.'/recipients', [
            'page' => $page,
            'limit' => $limit,
        ]);

        if (! isset($response->data->recipients) || ! is_array($response->data->recipients)) {
            throw new Exception('Invalid response received.', 1);
        }

        $statuses = [];
        foreach ($response->data->recipients as $recipient) {
            $status = new Recipient;
            $status->fromJson($recipient);

            $statuses[] = $status;
        }

        $paginationInfo = new PaginationInfo;
        $paginationInfo->fromJson($response->meta);

        return [$statuses, $paginationInfo];
    }

    /**
     * @param  int  $page
     * @param  int  $limit
     * @return array<int, InboxMessage[]|PaginationInfo>
     *
     * @throws Exception
     */
    public function fetchInbox(int $page = 0, int $limit = 10): array
    {
        $response = $this->client->get('/v1/messages/inbox', [
            'page' => $page,
            'limit' => $limit,
        ]);

        if (! isset($response->data->messages) || ! is_array($response->data->messages)) {
            throw new Exception('Invalid response received.', 1);
        }

        $messages = [];
        foreach ($response->data->messages as $message) {
            $msg = new InboxMessage;
            $msg->fromJson($message);

            $messages[] = $message;
        }

        $paginationInfo = new PaginationInfo;
        $paginationInfo->fromJson($response->meta);

        return [$messages, $paginationInfo];
    }

    /**
     * @throws Exception
     */
    public function createPattern(string $pattern, bool $isShared = false): Pattern
    {
        $response = $this->client->post('/v1/messages/patterns', [
            'pattern' => $pattern,
            'is_shared' => $isShared,
        ]);

        if (! isset($response->data->pattern)) {
            throw new Exception('Invalid response received.', 1);
        }

        $pattern = new Pattern;
        $pattern->fromJson($response->data->pattern);

        return $pattern;
    }

    /**
     * @param  string  $patternCode
     * @param  string  $originator
     * @param  string  $recipient
     * @param  array<string, mixed>  $values
     * @return int
     *
     * @throws Exception
     */
    public function sendPattern(string $patternCode, string $originator, string $recipient, array $values): int
    {
        foreach ($values as $key => $value) {
            $values[$key] = (string) $value;
        }

        $response = $this->client->post('/v1/messages/patterns/send', [
            'pattern_code' => $patternCode,
            'originator' => $originator,
            'recipient' => $recipient,
            'values' => $values,
        ]);

        if (! isset($response->data->bulk_id)) {
            throw new Exception('Invalid response received.', 1);
        }

        return $response->data->bulk_id;
    }
}
