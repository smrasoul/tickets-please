<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Policies\V1\UserPolicy;

class UserController extends ApiController
{

    protected $policyClass = UserPolicy::class;

    /**
     * @OA\Get(
     *      path="/api/v1/users",
     *      operationId="getUsersList",
     *      tags={"Users"},
     *      summary="Get list of Users",
     *      description="Returns list of Users",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="sort",
     *          in="query",
     *          description="Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=name,-createdAt",
     *          required=false,
     *      ),
     *      @OA\Parameter(
     *          name="filter[name]",
     *          in="query",
     *          description="Filter by name. Wildcards are supported. Example: \*fix\*
    ",
     *          required=false,
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *           response=401,
     *           description="Unauthenticated"
     *       )
     *     )
     */
    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(User::filter($filters)->paginate());
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users",
     *      operationId="storeUser",
     *      tags={"Users"},
     *      summary="Store new user",
     *      description="Returns user data",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *           response=401,
     *           description="Unauthenticated"
     *       ),
     *     @OA\Response(
     *            response=422,
     *            description="Validation Error"
     *        )
     *     )
     */
    public function store(StoreUserRequest $request)
    {
        if ($this->isAble('store', User::class)) {

            $model = $request->mappedAttributes();
            return new UserResource(User::create($model));

        }

        return $this->notAuthorized('You are not authorized to create that resource.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {

        if($this->include('tickets')){
            return new UserResource($user->load('tickets'));
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // PATCH
            // Policy Defined in AppServiceProvider because of the Folder Structure.
            if($this->isAble('update', $user)){

                $model = $request->mappedAttributes();
                $user->update($model);
                return new UserResource($user);

            }

            return $this->notAuthorized('You are not authorized to update that resource.');

    }

    public function replace(ReplaceUserRequest $request, User $user)
    {
        // PUT
            if($this->isAble('update', $user)){

                $model = $request->mappedAttributes();
                $user->update($model);
                return new UserResource($user);

            }

            return $this->notAuthorized('You are not authorized to update that resource.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {

            if($this->isAble('delete', $user)){

                $user->delete();
                return $this->ok('User successfully deleted');

            }

            return $this->notAuthorized('You are not authorized to delete that resource.');

    }
}
