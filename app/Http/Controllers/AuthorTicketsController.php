<?php

namespace App\Http\Controllers;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;

class AuthorTicketsController extends Controller
{
    public function index($author_id, TicketFilter $filter)
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author_id)
            ->filter($filter)
            ->paginate());
    }
}
