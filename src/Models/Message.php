<?php

namespace Pishran\IpPanel\Models;

class Message extends Base
{
    /**
     * Message tracking code
     *
     * @var int
     */
    public $bulkId;

    /**
     * Originator number
     *
     * @var string
     */
    public $number;

    /**
     * Message body
     *
     * @var string
     */
    public $message;

    /**
     * Message status
     *
     * @var string
     */
    public $status;

    /**
     * Message type
     *
     * @var string
     */
    public $type;

    /**
     * Message confirmation status
     *
     * @var string
     */
    public $confirmState;

    /**
     * Created at
     *
     * @var string
     */
    public $createdAt;

    /**
     * Message send time
     *
     * @var string
     */
    public $sentAt;

    /**
     * Message recipients count
     *
     * @var int
     */
    public $recipientsCount;

    /**
     * Recipients that passed validation
     *
     * @var int
     */
    public $validRecipientsCount;

    /**
     * Message number of pages
     *
     * @var int
     */
    public $page;

    /**
     * Message cost
     *
     * @var int
     */
    public $cost;

    /**
     * Message payback cost
     *
     * @var int
     */
    public $paybackCost;

    /**
     * Brief info about message
     *
     * @var string
     */
    public $description;
}
