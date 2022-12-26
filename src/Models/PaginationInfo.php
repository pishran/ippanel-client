<?php

namespace Pishran\IpPanel\Models;

class PaginationInfo extends Base
{
    /**
     * Total count
     *
     * @var int
     */
    public $total;

    /**
     * Pagination limit
     *
     * @var int
     */
    public $limit;

    /**
     * Current page
     *
     * @var int
     */
    public $page;

    /**
     * Total pages
     *
     * @var int
     */
    public $pages;

    /**
     * Preview resource
     *
     * @var string|null
     */
    public $prev;

    /**
     * Next resource
     *
     * @var string|null
     */
    public $next;
}
