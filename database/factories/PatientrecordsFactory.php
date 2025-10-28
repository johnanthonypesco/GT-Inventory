<?php

namespace Database\Factories;

use App\Models\Patientrecords;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientrecordsFactory extends Factory
{
    protected $model = Patientrecords::class;

    public function definition(): array
    {
        // List of sample barangays
        $barangays = [
            "Bago",
            "Concepcion",
            "Nazareth",
            "Padolina",
            "Palale",
            "Pias",
            "Poblacion Central",
            "Poblacion East",
            "Poblacion West",
            "Pulong Matong",
            "Rio Chico",
            "Sampaguita",
            "San Pedro (Pob.)"
        ];

        return [
            'patient_name' => $this->faker->name,
            'barangay' => $this->faker->randomElement($barangays),
            'purok' => $this->faker->randomElement(['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5']),
            'category' => $this->faker->randomElement(['Adult', 'Child', 'Senior']),
            'date_dispensed' => $this->faker->dateTimeBetween('-3 years', 'now'), // For seasonal data
        ];
    }
}
