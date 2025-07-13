<?php

namespace App\Http\Controllers;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
class AuthorTicketsController extends Controller
{
    public function index($author_id, TicketFilter $filter)
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author_id)
            ->filter($filter)
            ->paginate());
    }

    public function store(User $author , StoreTicketRequest $request)
    {

        $models = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $author->id //Implicitly from route model binding.
        ];

        return new TicketResource(Ticket::create($models));

    }

    // without Route model binding (Explicit)
//    public function store($author_id , StoreTicketRequest $request)
//    {
//
//        $models = [
//            'title' => $request->input('data.attributes.title'),
//            'description' => $request->input('data.attributes.description'),
//            'status' => $request->input('data.attributes.status'),
//            'user_id' => $author_id
//        ];
//
//        return new TicketResource(Ticket::create($models));
//
//    }
}
