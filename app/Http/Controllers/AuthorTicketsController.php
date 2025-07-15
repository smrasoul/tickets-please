<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthorTicketsController extends ApiController
{

    protected $policyClass = TicketPolicy::class;

    public function index($author_id, TicketFilter $filter)
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author_id)
            ->filter($filter)
            ->paginate());
    }

    public function store(StoreTicketRequest $request, $author_id)
    {

            if($this->isAble('store', Ticket::class)){

                $model = $request->mappedAttributes([
                    'author' => 'user_id'
                ]);

                return new TicketResource(Ticket::create($model));
            }

            return $this->error('You are not authorized to create that resource.', 401);

    }

    public function destroy($author_id, $ticket_id)
    {
        try{
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            if($this->isAble('delete', Ticket::class)) {

                $ticket->delete();
                return $this->ok('Ticket successfully deleted');

            }

            return $this->error('You are not authorized to delete that resource.', 401);

        } catch (ModelNotFoundException $exception){
            return $this->error('Ticket cannot be found', 404);
        }
    }

    public function replace(ReplaceTicketRequest $request,  $author_id, $ticket_id)
    {
        // PUT
        try {
            $ticket = Ticket::where('id', $ticket_id)
                            ->where('user_id', $author_id)
                            ->firstOrFail();

            if($this->isAble('replace', Ticket::class)) {

                $model = $request->mappedAttributes();
                $ticket->update($model);
                return new TicketResource($ticket);

            }

            return $this->error('You are not authorized to update that resource.', 401);

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        }
    }

    public function update(UpdateTicketRequest $request,  $author_id, $ticket_id)
    {
        // PUT
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            if($this->isAble('update', Ticket::class)) {

                $model = $request->mappedAttributes();
                $ticket->update($model);
                return new TicketResource($ticket);

            }

            return $this->error('You are not authorized to update that resource.', 401);

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        }
    }
}
