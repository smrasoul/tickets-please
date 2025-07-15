<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\V1\TicketPolicy;

class AuthorTicketsController extends ApiController
{

    protected $policyClass = TicketPolicy::class;

    public function index(User $author, TicketFilter $filter)
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author->id)
            ->filter($filter)
            ->paginate());
    }

    public function store(StoreTicketRequest $request)
    {

            if($this->isAble('store', Ticket::class)){

                $model = $request->mappedAttributes([
                    'author' => 'user_id'
                ]);

                return new TicketResource(Ticket::create($model));
            }

            return $this->notAuthorized('You are not authorized to create that resource.');

    }

    public function destroy(User $author, Ticket $ticket)
    {

            if($this->isAble('delete', $ticket)) {

                $ticket->delete();
                return $this->ok('Ticket successfully deleted');

            }

            return $this->notAuthorized('You are not authorized to delete that resource.');

    }

    public function replace(ReplaceTicketRequest $request,  User $author, Ticket $ticket)
    {
        // PUT
            if($this->isAble('replace', $ticket)) {

                $model = $request->mappedAttributes();
                $ticket->update($model);
                return new TicketResource($ticket);

            }

            return $this->notAuthorized('You are not authorized to update that resource.');

    }

    public function update(UpdateTicketRequest $request,  User $author, Ticket $ticket)
    {
        // PATCH
            if($this->isAble('update', $ticket)) {

                $model = $request->mappedAttributes();
                $ticket->update($model);
                return new TicketResource($ticket);

            }

            return $this->notAuthorized('You are not authorized to update that resource.');

    }
}
