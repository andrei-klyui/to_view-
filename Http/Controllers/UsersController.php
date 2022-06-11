<?php

namespace App\Http\Controllers;

use App\Events\UserCreated as EventUserCreated;
use App\Events\UserUpdated as EventUserUpdated;
use App\Http\Responses\ResponseGeneral;
use App\Models\Filters\FilterTrait;
use App\Models\Filters\UserFilter;
use App\Models\Office;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Validators\UserValidator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * @group User
 *
 * ###APIs for managing users
 *
 *
 * Class UsersController
 */
class UsersController extends Controller
{
    use FilterTrait;

    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var UserValidator
     */
    protected $userValidator;

    /**
     * UsersController constructor.
     * @param ResponseGeneral $responseStructured
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     * @param UserValidator $userValidator
     */
    public function __construct(
        ResponseGeneral $responseStructured,
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        UserValidator $userValidator
    ) {
        $this->responseStructured = $responseStructured;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->userValidator = $userValidator;
    }

    /**
     * User profile.
     *
     * ###Get user profile.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "entity": "user profile",
     *  "metadata": [
     *      "offices",
     *      "count issues, count posts"
     *  ]
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @return array
     */
    public function profile(): array
    {
        $user = Auth::user();
        $role = $this->userRepository->getUserRole($user);

        $offices = Office::all(['id', 'name']);

        $count['issues'] = $user->issues()->count();
        $count['posts'] = $user->posts()->count();

        $this->responseStructured->addEntity(array_merge($user->toArray(), ['role' => $role->value('name')]));
        $this->responseStructured->addMetadata($offices, 'offices');
        $this->responseStructured->addMetadata($count, 'count');
        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * User update profile.
     *
     * ###User profile update by request.
     *
     * @authenticated
     *
     * @bodyParam avatar                image               The avatar of the user(mimes:jpeg,jpg,png,bmp,gif;max:4096). Example: avatar.jpg
     * @bodyParam username              string  required    The username of the user(string;max:255;unique:users). Example: jone
     * @bodyParam name                  string  required    The name of the user(string;max:255). Example: Jone Jone
     * @bodyParam email                 email   required    The email of the user(string;max:255;email;unique:users). Example: email@email.com
     * @bodyParam office_id             int                 The office id of the user. Example: 1
     * @bodyParam password              string              The password of the user(string;min:6;max:255;confirmed). Example: pass
     * @bodyParam password_confirmation string              The password confirmation of the user. Example: pass
     * @bodyParam _method               string   required   The _method of the request - put. Example: put
     *
     * @response {
     *  "status": true,
     *  "entity": "updated user",
     *  "message": "message success"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @param Request $request
     * @return array
     */
    public function updateProfile(Request $request): array
    {
        try {
            $input = $request->except('_method');
            $this->userValidator->setId(Auth::id());
            $this->userValidator->with($input)->passesOrFail(ValidatorInterface::RULE_UPDATE);

            /**
             * @var User $user
             */
            $user = $this->userRepository->updateWithAvatar($input);
            $role = $user->roles()->value('name');

            event(new EventUserUpdated($user));

            $this->responseStructured->setStatus(true);
        } catch (ValidatorException $e) {
            $this->responseStructured->addMessage($e->getMessageBag(), 'errors');
        } catch (\Exception $e) {
            $this->responseStructured->addMessage($e->getMessage(), 'errors');
        } finally {
            if (! $this->responseStructured->getStatus()) {
                return $this->responseStructured->getResponse();
            }
        }

        $this->responseStructured->addEntity(array_merge($user->toArray(), ['role' => $role]));
        $this->responseStructured->addMessage(trans('users.success'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function updateProfileRole(Request $request, int $id): array
    {
        try {
            $data = $request->all();
            $user = User::find($id);

            $user->roles()->sync($data['role_id']);
            $user->save();

            $this->responseStructured->setStatus(true);
        } catch (ValidatorException $e) {
            $this->responseStructured->addMessage($e->getMessageBag(), 'errors');
        } catch (\Exception $e) {
            $this->responseStructured->addMessage($e->getMessage(), 'errors');
        } finally {
            if (! $this->responseStructured->getStatus()) {
                return $this->responseStructured->getResponse();
            }
        }

        $this->responseStructured->setStatus(true);
        $this->responseStructured->addMessage(trans('users.success'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * Remove user profile avatar.
     *
     * ###Remove user profile avatar
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "message": "message success"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @return array
     */
    public function removeProfileAvatar(): array
    {
        $user = Auth::user();

        return $this->removeAvatar($user);
    }

    /**
     * A list of users
     * and search users.
     *
     * ###Get list of users and search users.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "entity": "a list of users",
     * }
     *
     * @return array
     * @throws AuthorizationException
     */
    public function index(UserFilter $filters): array
    {
        $this->authorize('list', User::class);
        $this->responseStructured->addMetadata($this->roleRepository->all(), 'roles');
        $this->responseStructured->addMetadata(Office::all(['id', 'name']), 'offices');
        $this->responseStructured->addEntity($this->userRepository->getList($filters));
        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * Create user.
     *
     * ###Get data for create user.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "metadata": "a list of roles and offices",
     * }
     *
     * @return array
     * @throws AuthorizationException
     */
    public function create(): array
    {
        $this->authorize('create', User::class);

        $roles = $this->roleRepository->all(['id', 'name', 'title']);
        $offices = Office::all(['id', 'name']);

        $this->responseStructured->addMetadata($roles, 'roles');
        $this->responseStructured->addMetadata($offices, 'offices');

        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * Store user.
     *
     * ###Create a user in the system.
     *
     * @authenticated
     *
     * @bodyParam avatar                image               The avatar of the user(mimes:jpeg,jpg,png,bmp,gif;max:4096). Example: avatar.jpg
     * @bodyParam username              string  required    The username of the user(string;max:255;unique:users). Example: jone
     * @bodyParam name                  string  required    The name of the user(string;max:255). Example: Jone Jone
     * @bodyParam email                 email   required    The email of the user(string;max:255;email;unique:users). Example: email@email.com
     * @bodyParam office_id             int                 The office id of the user. Example: 1
     * @bodyParam role_id               int     required    The role id of the user. Example: 1
     * @bodyParam password              string              The password of the user(string;min:6;max:255;confirmed). Example: pass
     * @bodyParam password_confirmation string              The password confirmation of the user. Example: pass
     *
     * @response {
     *  "status": true,
     *  "entity": "created user",
     *  "message": "message success"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     * @param Request $request
     * @return array
     * @throws AuthorizationException
     */
    public function store(Request $request): array
    {
        $this->authorize('create', User::class);
        try {
            $this->userValidator->with($request->all())->passesOrFail(UserValidator::RULE_CREATE_NEW_USER);

            $user = $this->userRepository->createWithAvatar($request->all());

            $this->responseStructured->setStatus(true);
        } catch (ValidatorException $e) {
            $this->responseStructured->addMessage($e->getMessageBag(), 'errors');
        } catch (\Exception $e) {
            $this->responseStructured->addMessage($e->getMessage(), 'errors');
        } finally {
            if (! $this->responseStructured->getStatus()) {
                return $this->responseStructured->getResponse();
            }
        }

        event(new EventUserCreated($user));

        $this->responseStructured->addEntity($user);
        $this->responseStructured->addMessage(trans('users.success'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * Edit user.
     *
     * ###Get data for edit user.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "entity": "user data",
     *  "metadata": "a list of roles and offices",
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     * @param int $id
     * @return array
     * @throws AuthorizationException
     */
    public function edit(int $id): array
    {
        /** @var User $user */
        $user = $this->userRepository->find($id);

        $this->authorize('update', $user);

        $user->loadMissing('roles');

        $roles = $this->roleRepository->all();
        $offices = Office::all(['id', 'name']);

        $this->responseStructured->addEntity($user);
        $this->responseStructured->addMetadata($roles, 'roles');
        $this->responseStructured->addMetadata($offices, 'offices');

        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * User update.
     *
     * ###User update by request.
     *
     * @authenticated
     *
     * @bodyParam avatar                image               The avatar of the user(mimes:jpeg,jpg,png,bmp,gif;max:4096). Example: avatar.jpg
     * @bodyParam username              string  required    The username of the user(string;max:255;unique:users). Example: jone
     * @bodyParam name                  string  required    The name of the user(string;max:255). Example: Jone Jone
     * @bodyParam email                 email   required    The email of the user(string;max:255;email;unique:users). Example: email@email.com
     * @bodyParam office_id             int                 The office id of the user. Example: 1
     * @bodyParam password              string              The password of the user(string;min:6;max:255;confirmed). Example: pass
     * @bodyParam role_id               int     required    The role id of the user. Example: 1
     * @bodyParam password_confirmation string              The password confirmation of the user. Example: pass
     * @bodyParam _method               string  required   The _method of the request - put. Example: put
     *
     * @response {
     *  "status": true,
     *  "entity": "updated user",
     *  "message": "message success"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @param Request $request
     * @param int $id
     * @return array
     * @throws AuthorizationException
     */
    public function update(Request $request, int $id): array
    {
        /** @var User $user */
        $user = $this->userRepository->find($id);

        $this->authorize('update', $user);

        try {
            $input = $request->except('_method');
            $this->userValidator->setId($id);
            $this->userValidator->with($input)->passesOrFail(UserValidator::RULE_UPDATE_USER);

            $this->userRepository->updateByUser($user, $input);

            event(new EventUserUpdated($user));

            $this->responseStructured->setStatus(true);
        } catch (ValidatorException $e) {
            $this->responseStructured->addMessage($e->getMessageBag(), 'errors');
        } catch (\Exception $e) {
            $this->responseStructured->addMessage($e->getMessage(), 'errors');
        } finally {
            if (! $this->responseStructured->getStatus()) {
                return $this->responseStructured->getResponse();
            }
        }
        $user->load('roles');

        $this->responseStructured->addEntity($user);
        $this->responseStructured->addMessage(trans('users.success'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * Remove user.
     *
     * ###Remove user
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "message": "message success"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @param int $id
     * @return array
     * @throws AuthorizationException
     * @throws \Exception
     */
    public function destroy(int $id): array
    {
        /** @var User $user */
        $user = $this->userRepository->find($id);

        $this->authorize('delete', $user);

        $user->delete();

        $this->responseStructured->setStatus(true);
        $this->responseStructured->addMessage(trans('users.delete.success'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * Remove user avatar.
     *
     * ###Remove user avatar
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "message": "message success"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @param int $id
     * @return array
     * @throws AuthorizationException
     */
    public function removeUserAvatar(int $id): array
    {
        /** @var User $user */
        $user = $this->userRepository->find($id);

        $this->authorize('removeUserAvatar', $user);

        return $this->removeAvatar($user);
    }

    /**
     * Remove user avatar.
     *
     * @param User $user
     * @return array
     */
    private function removeAvatar(User $user): array
    {
        if (empty($user->avatar)) {
            $this->responseStructured->addMessage(trans('users.remove-avatar.not-exist'), 'errors');

            return $this->responseStructured->getResponse();
        }

        try {
            $this->userRepository->removeUserAvatar($user);

            $this->responseStructured->setStatus(true);
        } catch (\Exception $e) {
            $this->responseStructured->addMessage($e->getMessage(), 'errors');

            return $this->responseStructured->getResponse();
        }

        $this->responseStructured->addMessage(trans('users.remove-avatar.success'), 'success');

        return $this->responseStructured->getResponse();
    }

    /**
     * Get collection of users with a given role.
     *
     * @param string $roles
     * @return array
     */
    public function filterByRoles(string $roles): array
    {
        $roles = explode(',', $roles);

        $users = $this->userRepository->getUsersByRoles($roles);

        $this->responseStructured->setStatus(true);
        $this->responseStructured->addEntity($users);

        return $this->responseStructured->getResponse();
    }
}
