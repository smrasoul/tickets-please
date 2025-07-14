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
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthorTicketsController extends ApiController
{
    public function index($author_id, TicketFilter $filter)
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author_id)
            ->filter($filter)
            ->paginate());
    }

//    public function store(User $author , StoreTicketRequest $request)
//    {
//
//        $models = [
//            'title' => $request->input('data.attributes.title'),
//            'description' => $request->input('data.attributes.description'),
//            'status' => $request->input('data.attributes.status'),
//            'user_id' => $author->id //Implicitly from route model binding.
//        ];
//
//        return new TicketResource(Ticket::create($models));
//
//    }

    // without Route model binding (Explicit)
    public function store($author_id , StoreTicketRequest $request)
    {

        $model = $request->mappedAttributes($author_id);

        return new TicketResource(Ticket::create($model));

    }

    public function destroy($author_id, $ticket_id)
    {
        try{
            $ticket = Ticket::findOrFail($ticket_id);

            if($ticket->user_id == $author_id){
                $ticket->delete();
                return $this->ok('Ticket successfully deleted');
            }

            return $this->error('Ticket cannot be found', 404);

        } catch (ModelNotFoundException $exception){
            return $this->error('Ticket cannot be found', 404);
        }
    }

    public function replace(ReplaceTicketRequest $request,  $author_id, $ticket_id)
    {
        // PUT
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            if($ticket->user_id == $author_id) {


                $model = $request->mappedAttributes();

                $ticket->update($model);

                return new TicketResource($ticket);
            }

            // TODO: ticket doesn't belong to user

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        }
    }

    public function update(UpdateTicketRequest $request,  $author_id, $ticket_id)
    {
        // PUT
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            if($ticket->user_id == $author_id) {


                $model = $request->mappedAttributes();

                $ticket->update($model);

                return new TicketResource($ticket);
            }

            // TODO: ticket doesn't belong to user

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        }
    }
}
