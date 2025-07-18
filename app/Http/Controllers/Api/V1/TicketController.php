<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;


class TicketController extends ApiController
{

    protected $policyClass = TicketPolicy::class;

    /**
     *
     * Get all tickets
     *
     * @group Managing Tickets
     *
     * @queryParam sort string
     * Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=title,-CreatedAt
     *
     * @queryParam filter[status]
     * Filter by status code: A, C, H, H. No-example
     *
     * @queryParam filter[title]
     * Filter by title. Wildcards are supported. Example: *fix*
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     *
     * Create a tickets
     *
     * Create a new ticket. Users can only create tickets for themselves. Managers can create tickets for any user.
     *
     * @group Managing Tickets
     *
     */
    public function store(StoreTicketRequest $request)
    {

            if ($this->isAble('store', Ticket::class)){

                $model = $request->mappedAttributes();

                return new TicketResource(Ticket::create($model));

            }

            return $this->notAuthorized('You are not authorized to store that resource.');


    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {

        if ($this->include('author')) {
            return new TicketResource($ticket->load('author'));
        }

        return new TicketResource($ticket);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        // PATCH
            // Policy Defined in AppServiceProvider because of the Folder Structure.
            if($this->isAble('update', $ticket)){

                $model = $request->mappedAttributes();
                $ticket->update($model);
                return new TicketResource($ticket);

            }

            return $this->notAuthorized('You are not authorized to update that resource.');


    }

    public function replace(ReplaceTicketRequest $request, Ticket $ticket)
    {
        // PUT
            if($this->isAble('replace', $ticket)){

                $model = $request->mappedAttributes();
                $ticket->update($model);
                return new TicketResource($ticket);

            }

            return $this->notAuthorized('You are not authorized to update that resource.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {

        if ($this->isAble('delete', $ticket)) {

            $ticket->delete();
            return $this->ok('Ticket successfully deleted');

        }

        return $this->notAuthorized('You are not authorized to delete that resource.');


    }
}
