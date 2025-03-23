<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Empleado;
use Livewire\Attributes\Computed;

class Empleados extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $nombre, $correo;

    #[Computed]
	public function filteredEmpleados()
	{
		$keyWord = '%' . $this->keyWord . '%';
		return Empleado::latest()
			->where(function ($query) use ($keyWord) {
				$query
						->orWhere('nombre', 'LIKE', $keyWord)
						->orWhere('correo', 'LIKE', $keyWord);
			})
			->paginate(10);
	}

	public function render()
	{
		return view('livewire.empleados.view', [
			'empleados' => $this->filteredEmpleados,
		]);
	}
	
    public function cancel()
    {
        $this->reset();
    }

    public function save()
    {
        $this->validate([
		'nombre' => 'required',
		'correo' => 'required',
        ]);

        Empleado::updateOrCreate(
			['id' => $this->selected_id],
			[
				'nombre' => $this-> nombre,
				'correo' => $this-> correo
			]
		);

        $message = $this->selected_id ? 'Empleado Successfully updated.' : 'Empleado Successfully created.';
		$this->dispatch('closeModal');
        $this->reset();
		session()->flash('message', $message);
    }

    public function edit($id)
    {
        $this->selected_id = $id;
		$this->fill(Empleado::findOrFail($id)->toArray());
    }

    public function destroy($id)
    {
        if ($id) {
            Empleado::where('id', $id)->delete();
        }
    }
}