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
    protected $userRepository;
    protected $userService;
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

    public function index()
    {
        $users = $this->userRepository->getAll();
        return view('pages.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = $this->userRepository->findById($id);
        return view('pages.users.detail', compact('user'));
    }

    public function add()
    {
        $roles = ['admin', 'user', 'finance', 'redactor', 'manager'];
        return view('pages.users.add', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $result = $this->userService->createUser($request->validated());

        return redirect()->route('users.index')
            ->with('success', "Пользователь создан! Пароль: {$result['password']}");
    }

    public function delete(Request $request)
    {
        $this->userService->deleteUser($request->id);

        return redirect()->route('users.index')
            ->with('success', "Пользователь удален");
    }

    public function profile()
    {
        $user = auth()->user();
        $picture = $this->pictureRepository->getByEntity('user', $user->id)->first();

        return view('pages.profile', compact('user', 'picture'));
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = auth()->user();
        $path = $request->file('avatar')->store('avatars', 'public');

        $this->userService->updateAvatar($user->id, $path);

        return redirect()->route('profile')->with('success', 'Аватар обновлён');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->userService->updateUser(auth()->id(), $validated);

        return redirect()->route('profile')->with('success', 'Имя обновлено');
    }

    public function edit($id)
    {
        $user = $this->userRepository->findById($id);
        $roles = ['admin', 'user', 'finance', 'redactor', 'manager'];
        return view('pages.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $this->userService->updateUser($id, $request->validated());

        return redirect()->route('users.show', ['id' => $id])->with('success', 'Пользователь обновлён');
    }
}
