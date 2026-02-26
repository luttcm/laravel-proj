<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\PictureRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /** @var UserRepository */
    protected $userRepository;
    /** @var UserService */
    protected $userService;
    /** @var PictureRepository */
    protected $pictureRepository;

    public function __construct(
        UserRepository $userRepository,
        UserService $userService,
        PictureRepository $pictureRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->pictureRepository = $pictureRepository;
    }

    public function index(): \Illuminate\View\View
    {
        $users = $this->userRepository->getAll();
        return view('pages.users.index', compact('users'));
    }

    public function show(int $id): \Illuminate\View\View
    {
        $user = $this->userRepository->findById($id);
        return view('pages.users.detail', compact('user'));
    }

    public function add(): \Illuminate\View\View
    {
        $roles = ['admin', 'user', 'finance', 'redactor', 'manager'];
        return view('pages.users.add', compact('roles'));
    }

    public function store(StoreUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $result = $this->userService->createUser($request->validated());

        return redirect()->route('users.index')
            ->with('success', "Пользователь создан! Пароль: {$result['password']}");
    }

    public function delete(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->userService->deleteUser((int)$request->id);

        return redirect()->route('users.index')
            ->with('success', "Пользователь удален");
    }

    public function profile(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $picture = $this->pictureRepository->getByEntity('user', $user->id)->first();

        return view('pages.profile', compact('user', 'picture'));
    }

    public function updateAvatar(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $request->file('avatar');
        $path = $file->store('avatars', 'public');

        $this->userService->updateAvatar($user->id, (string)$path);

        return redirect()->route('profile')->with('success', 'Аватар обновлён');
    }

    public function updateProfile(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->userService->updateUser((int)auth()->id(), $validated);

        return redirect()->route('profile')->with('success', 'Имя обновлено');
    }

    public function edit(int $id): \Illuminate\View\View
    {
        $user = $this->userRepository->findById($id);
        $roles = ['admin', 'user', 'finance', 'redactor', 'manager'];
        return view('pages.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $this->userService->updateUser($id, $request->validated());

        return redirect()->route('users.show', ['id' => $id])->with('success', 'Пользователь обновлён');
    }
}
