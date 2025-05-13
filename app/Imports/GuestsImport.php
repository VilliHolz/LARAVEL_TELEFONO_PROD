<?php

namespace App\Imports;

use App\Models\Guest;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuestsImport implements ToModel, WithHeadingRow
{
    protected $event_id;

    public function __construct($event_id)
    {
        $this->event_id = $event_id;
    }

    /**
    * @param array $row
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Verificar si el invitado ya existe en este evento
        $guest = Guest::firstOrCreate(
            [
                'name' => $row['nombre'],                
                'email' => $row['email'],
                'flag' => $row['flag'],
                'code' => $row['code'],
                'phone' => $row['telefono'],
                'dietary_restrictions' => [],
                'user_id' => auth()->user()->id,
                'event_id' => $this->event_id,
            ]
        );

        return $guest;
    }
}

