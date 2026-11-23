<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceStep;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceStepsSeeder extends Seeder
{
    public function run(): void
    {
        ServiceStep::query()->delete();

        $legacyStepMap = $this->loadLegacyStepMap();
        $services = Service::with('category')->orderBy('id')->get();

        foreach ($services as $service) {
            /** @var Service $service */
            $steps = $legacyStepMap[$this->lookupKey($service->title)]
                ?? $this->fallbackSteps($service);

            foreach ($steps as $index => $stepData) {
                ServiceStep::create([
                    'service_id' => $service->id,
                    'sort_order' => $index + 1,
                    'title' => $stepData['title'],
                    'description' => $stepData['description'],
                ]);
            }
        }
    }

    private function loadLegacyStepMap(): array
    {
        $legacyPath = dirname(base_path()) . DIRECTORY_SEPARATOR . 'alcaldia-page' . DIRECTORY_SEPARATOR . 'short-circuit-repair.php';

        if (! is_file($legacyPath)) {
            return [];
        }

        $contents = file_get_contents($legacyPath);

        if ($contents === false || $contents === '') {
            return [];
        }

        preg_match_all(
            '/"nombre"\s*=>\s*"(?<title>(?:\\\\.|[^"])*)".*?"pasos"\s*=>\s*"(?<steps>(?:\\\\.|[^"])*)"/s',
            $contents,
            $matches,
            PREG_SET_ORDER,
        );

        $stepMap = [];

        foreach ($matches as $match) {
            $title = stripslashes($match['title']);
            $steps = $this->splitLegacySteps(stripslashes($match['steps']));

            if ($steps === []) {
                continue;
            }

            $stepMap[$this->lookupKey($title)] = array_map(
                fn(string $stepText, int $index) => [
                    'title' => count($steps) > 1 ? 'Paso ' . ($index + 1) : 'Paso único',
                    'description' => $stepText,
                ],
                $steps,
                array_keys($steps),
            );
        }

        return $stepMap;
    }

    private function splitLegacySteps(string $legacyText): array
    {
        $normalized = trim(str_replace(["\r\n", "\r"], "\n", $legacyText));

        if ($normalized === '') {
            return [];
        }

        $parts = preg_split('/(?:^|\n|\s)(?:\d+\.|[a-z]\))\s*/i', $normalized, -1, PREG_SPLIT_NO_EMPTY);

        if ($parts === false || $parts === []) {
            $parts = [$normalized];
        }

        return array_values(array_filter(array_map(
            static fn(string $part): string => trim(preg_replace('/\s+/', ' ', $part) ?? ''),
            $parts,
        )));
    }

    private function fallbackSteps(Service $service): array
    {
        return $service->kind === Service::KIND_PROCEDURE
            ? [
                [
                    'title' => 'Presentar la solicitud de ' . $service->title,
                    'description' => 'Lleve la documentación requerida y complete el formulario en la ventanilla correspondiente.',
                ],
                [
                    'title' => 'Realizar validación o pago correspondiente',
                    'description' => 'Espere la revisión de datos y complete el arancel o validación que aplique al trámite.',
                ],
                [
                    'title' => 'Retirar el documento final',
                    'description' => 'Reciba la constancia, resolución o documento emitido por la municipalidad.',
                ],
            ]
            : [
                [
                    'title' => 'Registrar la solicitud de ' . $service->title,
                    'description' => 'Acuda a la unidad correspondiente e indique la necesidad que desea atender.',
                ],
                [
                    'title' => 'Presentar documentación de respaldo',
                    'description' => 'Entregue documentos de identidad, referencias o evidencia relacionada con el caso.',
                ],
                [
                    'title' => 'Esperar evaluación y respuesta',
                    'description' => 'El equipo municipal revisará el caso y le notificará el seguimiento o apoyo definido.',
                ],
            ];
    }

    private function lookupKey(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^\pL\pN]+/u', ' ')
            ->squish()
            ->toString();
    }
}
