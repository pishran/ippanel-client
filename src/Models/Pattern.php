<?php

namespace Pishran\IpPanel\Models;

class Pattern extends Base
{
    /**
     * Pattern unique code
     *
     * @var string
     */
    public $code;

    /**
     * Pattern status
     *
     * @var string
     */
    public $status;

    /**
     * Pattern content
     *
     * @var string
     */
    public $message;

    /**
     * Pattern shared or not
     *
     * @var bool
     */
    public $isShared;
}
