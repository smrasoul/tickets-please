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
use Illuminate\Auth\Access\AuthorizationException;
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

        try {

            $this->isAble('store', Ticket::class);

            $model = $request->mappedAttributes([
                'author' => 'user_id'
            ]);

            return new TicketResource(Ticket::create($model));

        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to create that resource.', 401);
        }

    }

    public function destroy($author_id, $ticket_id)
    {
        try{
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            $this->isAble('delete', $ticket);

            $ticket->delete();

            return $this->ok('Ticket successfully deleted');

        } catch (ModelNotFoundException $exception){
            return $this->error('Ticket cannot be found', 404);
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to delete that resource.', 401);
        }
    }

    public function replace(ReplaceTicketRequest $request,  $author_id, $ticket_id)
    {
        // PUT
        try {
            $ticket = Ticket::where('id', $ticket_id)
                            ->where('user_id', $author_id)
                            ->firstOrFail();

            $this->isAble('replace', $ticket);

            $model = $request->mappedAttributes();

            $ticket->update($model);

            return new TicketResource($ticket);


            // TODO: ticket doesn't belong to user

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to update that resource.', 401);
        }
    }

    public function update(UpdateTicketRequest $request,  $author_id, $ticket_id)
    {
        // PUT
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            $this->isAble('update', $ticket);

                $model = $request->mappedAttributes();

                $ticket->update($model);

                return new TicketResource($ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        }catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to update that resource.', 401);
        }
    }
}
