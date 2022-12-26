<?php

namespace Pishran\IpPanel\Models;

class Response extends Base
{
    /**
     * HTTP status code
     *
     * @var string
     */
    public $status;

    /**
     * IPPanel response code
     *
     * @var int
     */
    public $code;

    /**
     * Response data
     *
     * @var mixed
     */
    public $data;

    /**
     * Meta data
     *
     * @var mixed
     */
    public $meta;
}
