<?php

declare(strict_types=1);

function format_shift_time(string $start, string $end): string
{
    return date('g:i A', strtotime($start)) . ' - ' . date('g:i A', strtotime($end));
}
