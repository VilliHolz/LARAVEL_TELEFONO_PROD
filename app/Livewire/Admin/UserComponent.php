<?php

namespace App\Livewire\Admin;

use App\Models\Branch;
use App\Models\Plane;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $opcional = '';
    public $name, $email, $password, $password_confirmation, $address, $user_id, $branch_id;
    public $isOpen = 0;

    public $loading = false;

    protected $queryString = ['search'];

    protected $listeners = ['delete'];

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $users = User::with('branch')->where('name', 'like', $searchTerm)
            ->orderBy('id', 'desc')
            ->paginate(9);

        $branches = Branch::where('status', 'Activo')->get();

        return view('livewire.admin.user-component', compact('users', 'branches'))->extends('admin.layouts.app');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->resetInputFields();
        $this->resetValidation();
        $this->isOpen = false;
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->address = '';
        $this->password = '';
        $this->branch_id = '';
        $this->password_confirmation = '';
        $this->opcional = '';
        $this->user_id = null;
    }

    public function store()
    {
        // Validación condicional
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
        ];

        if (!$this->user_id) {
            $rules['password'] = 'required|confirmed';
            $rules['password_confirmation'] = 'required';
        }

        $this->validate($rules);

        // Data para actualizar o crear
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'branch_id' => !empty($this->branch_id) ? $this->branch_id : null,
            'status' => 'Activo'
        ];

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        $user = User::updateOrCreate(['id' => $this->user_id], $data);

        // Asignar rol Admin solo si es un nuevo registro
        if (!$this->user_id) {
            $user->assignRole('Admin');
        }

        session()->flash('message', $this->user_id ? 'Usuario Actualizado.' : 'Usuario Creado.');

        $this->closeModal();
        $this->resetInputFields();
    }


    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $this->user_id = $id;
        $this->name = $usuario->name;
        $this->email = $usuario->email;
        $this->branch_id = $usuario->branch_id;
        $this->password = '';
        $this->opcional = '(Opcional)';
        $this->address = $usuario->address;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }


    public function delete($valor)
    {
        User::find($valor['id'])->delete();

        session()->flash('message', 'Usuario Eliminada.');
    }

    public function changeStatus($userId, $newStatus)
    {
        $this->loading = true;

        // Encontrar al usuario y actualizar el estado
        $user = User::find($userId);
        $user->status = $newStatus;
        $user->save();

        // if ($newStatus === 'Activo') {
        //     $subject = 'Tu cuenta ha sido activada';
        // } else {
        //     $subject = 'Tu cuenta ha sido desactivada';
        // }
        // Enviar correo
        //Mail::to($user->email)->send(new UserStatusChanged($user, $subject));
        // Opcional: Mostrar un mensaje de éxito
        $this->loading = false;
        session()->flash('message', 'Estado actualizado correctamente.');
    }
}
