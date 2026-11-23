<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceRequirement;
use App\Models\ServiceStep;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds all services and procedures with their full legacy data:
 * título, descripción, costo, tiempo estimado, requisitos, documentación,
 * pasos, horario, icono y categoría.
 *
 * Source: apps/alcaldia-page/short-circuit-repair.php (trámites)
 */
class ServicesSeeder extends Seeder
{
    /**
     * Parse a legacy string into a clean array of items.
     * Handles: "1. Foo. 2. Bar." / "a) Foo b) Bar" / "plain sentence"
     */
    private function parseList(string $text): array
    {
        $text = trim($text);
        if ($text === '') {
            return [];
        }

        // Try numbered/lettered patterns: 1. / 1) / a. / a)
        $parts = preg_split('/(?:^|\n|\s*)(?:\d+[.)]\s*|[a-z][.)]\s*)/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        if ($parts && count($parts) > 1) {
            return array_values(array_filter(
                array_map(fn($p) => trim(preg_replace('/\s+/', ' ', $p) ?? ''), $parts),
                fn($p) => $p !== '',
            ));
        }

        // Try newline-separated
        $parts = array_filter(array_map('trim', explode("\n", $text)), fn($p) => $p !== '');
        if (count($parts) > 1) {
            return array_values($parts);
        }

        return [$text];
    }

    private function uniqueSlug(string $base, ?int $categoryId): string
    {
        $slug = $base;
        $i = 1;
        while (Service::where('slug', $slug)->where('category_id', $categoryId)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private function createServiceWithRelations(array $data, ServiceCategory $category, int $order, string $kind): void
    {
        $slug = $this->uniqueSlug(Str::slug($data['nombre']), $category->id);

        $documentation = isset($data['documentacion'])
            ? $this->parseList($data['documentacion'])
            : [];

        $service = Service::create([
            'kind'           => $kind,
            'category_id'    => $category->id,
            'title'          => $data['nombre'],
            'slug'           => $slug,
            'description'    => $data['descripcion'] ?? null,
            'cost'           => $data['costo'] ?? null,
            'estimated_time' => $data['tiempo'] ?? null,
            'schedule'       => $data['horario'] ?? 'Lunes a Viernes 8 AM - 4 PM',
            'icon'           => $data['icono'] ?? null,
            'documentation'  => $documentation,
            'is_active'      => true,
            'order'          => $order,
        ]);

        $reqList = isset($data['requisitos']) ? $this->parseList($data['requisitos']) : [];
        foreach ($reqList as $i => $reqText) {
            // Truncate title to 250 chars; overflow goes to description
            $title = mb_substr($reqText, 0, 250);
            $extra = mb_strlen($reqText) > 250 ? mb_substr($reqText, 250) : null;
            ServiceRequirement::create([
                'service_id' => $service->id,
                'title'      => $title,
                'description' => $extra,
                'is_basic'   => true,
                'sort_order' => $i + 1,
            ]);
        }

        $stepList = isset($data['pasos']) ? $this->parseList($data['pasos']) : [];
        foreach ($stepList as $i => $stepText) {
            ServiceStep::create([
                'service_id'  => $service->id,
                'title'       => count($stepList) > 1 ? 'Paso ' . ($i + 1) : 'Procedimiento',
                'description' => $stepText,
                'sort_order'  => $i + 1,
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MAIN ENTRY POINT
    // ─────────────────────────────────────────────────────────────────────────
    public function run(): void
    {
        // Wipe existing data
        ServiceStep::query()->delete();
        ServiceRequirement::query()->delete();
        Service::withTrashed()->forceDelete();

        $this->seedProcedures();
        $this->seedMunicipalServices();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TRÁMITES (procedures)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedProcedures(): void
    {
        $order = 1;
        foreach ($this->tramitesCatalog() as $categoryName => $items) {
            $category = ServiceCategory::firstOrCreate(
                ['slug' => Str::slug($categoryName), 'type' => ServiceCategory::TYPE_TRAMITE],
                ['name' => $categoryName, 'type' => ServiceCategory::TYPE_TRAMITE, 'is_active' => true, 'position' => $order],
            );
            foreach ($items as $data) {
                $this->createServiceWithRelations($data, $category, $order++, Service::KIND_PROCEDURE);
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SERVICIOS MUNICIPALES (services)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedMunicipalServices(): void
    {
        $order = 200;
        foreach ($this->serviciosCatalog() as $categoryName => $items) {
            $category = ServiceCategory::firstOrCreate(
                ['slug' => Str::slug($categoryName), 'type' => ServiceCategory::TYPE_SERVICE],
                ['name' => $categoryName, 'type' => ServiceCategory::TYPE_SERVICE, 'is_active' => true, 'position' => $order],
            );
            foreach ($items as $data) {
                $this->createServiceWithRelations($data, $category, $order++, Service::KIND_SERVICE);
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DATA — TRÁMITES
    // ─────────────────────────────────────────────────────────────────────────
    private function tramitesCatalog(): array
    {
        return [
            'Trámites Legales' => [
                [
                    'nombre'       => 'Legalización de Documentos',
                    'descripcion'  => 'Proceso de validación legal de documentos municipales.',
                    'costo'        => '$20',
                    'tiempo'       => '2 días hábiles',
                    'requisitos'   => 'Documento original, identificación.',
                    'documentacion' => 'Documento a legalizar, copia de identificación.',
                    'pasos'        => '1. Acudir a la alcaldía. 2. Presentar documento. 3. Pagar tasa.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-file-signature',
                ],
            ],
            'Constancias' => [
                [
                    'nombre'       => 'Solicitud para Acta Catastral de ANDA',
                    'descripcion'  => 'Solicitud para obtener el acta catastral de ANDA.',
                    'costo'        => '$10.50',
                    'tiempo'       => '2 días hábiles',
                    'requisitos'   => 'Estar solvente con el pago de tasas por servicios del inmueble. El trámite debe ser personal. En caso contrario, el representante debe presentar un poder especial.',
                    'documentacion' => '1. Copia de escritura del inmueble. 2. Copia de DUI del propietario. 3. Solvencia del inmueble.',
                    'pasos'        => '1. Presentar toda la documentación al departamento de registro tributario de esta municipalidad. 2. Esperar la notificación a través de llamada para la respectiva cancelación de la constancia. 3. Luego de la cancelación, pasar por el retiro de la constancia en el departamento de registro tributario de esta municipalidad.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-map',
                ],
                [
                    'nombre'       => 'Solicitud para Acta Catastral EEO',
                    'descripcion'  => 'Solicitud para obtener el acta catastral EEO.',
                    'costo'        => '$10.50',
                    'tiempo'       => '2 días hábiles',
                    'requisitos'   => 'Estar solvente con el pago de tasas por servicios del inmueble. El trámite debe ser personal. En caso contrario, el representante debe presentar un poder especial.',
                    'documentacion' => '1. Copia de escritura del inmueble. 2. Copia de DUI del propietario. 3. Solvencia del inmueble.',
                    'pasos'        => '1. Presentar toda la documentación al departamento de registro tributario. 2. Esperar la notificación a través de llamada para la respectiva cancelación. 3. Pasar por el retiro de la constancia en el departamento de registro tributario.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-map',
                ],
            ],
            'Trámites Empresariales' => [
                [
                    'nombre'       => 'Vialidad Municipal',
                    'descripcion'  => 'Vialidad y Solvencia Municipal Inscrito.',
                    'costo'        => '$6.06',
                    'tiempo'       => '1 día hábil',
                    'requisitos'   => 'Estar inscrito en el sistema de Registro Tributario Municipal y estar solvente de todas las propiedades.',
                    'documentacion' => 'Copia de DUI y escritura de la propiedad.',
                    'pasos'        => '1. Solicitar solvencia. 2. Validar pagos. 3. Emitir vialidad o solvencia.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-id-card',
                ],
                [
                    'nombre'       => 'Vialidad No Rural',
                    'descripcion'  => 'Vialidad y Solvencia Municipal No Inscrito Rural.',
                    'costo'        => '$21.81',
                    'tiempo'       => '1 día hábil',
                    'requisitos'   => '1. Dependen de la inspección que se le realice al usuario en la unidad de Fiscalización; en caso este recibiendo servicios la propiedad, se le prescribirá en el plazo de tres años, con facultad en el Art. 62 ley general tributaria municipal. 2. Estar solvente con todas las propiedades.',
                    'documentacion' => 'Copia de Escritura, DUI.',
                    'pasos'        => '1. Presentar copia de DUI, escritura en atención al usuario. 2. Esperar indicaciones.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-city',
                ],
                [
                    'nombre'       => 'Vialidad No Urbano',
                    'descripcion'  => 'Vialidad y Solvencia Municipal No Inscrito Urbano.',
                    'costo'        => '$16.53',
                    'tiempo'       => '1 día hábil',
                    'requisitos'   => '1. Dependen de la inspección que se le realice al usuario en la unidad de Fiscalización; en caso este recibiendo servicios la propiedad, se le prescribirá en el plazo de tres años, con facultad en el Art. 62 ley general tributaria municipal. 2. Estar solvente con todas las propiedades.',
                    'documentacion' => 'Copia de Escritura, DUI.',
                    'pasos'        => '1. Presentar copia de DUI, escritura en atención al usuario. 2. Esperar indicaciones.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-mountain',
                ],
                [
                    'nombre'       => 'Estado de Cuenta Inmueble',
                    'descripcion'  => 'Emisión de Estado de Cuenta Inmueble.',
                    'costo'        => 'Gratis',
                    'tiempo'       => '15 días hábiles',
                    'requisitos'   => 'Registro de empresa, identificación.',
                    'documentacion' => 'Certificado de registro, documento de identidad.',
                    'pasos'        => '1. Presentar solicitud. 2. Validación de empresa. 3. Emitir estado de cuenta.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-building',
                ],
                [
                    'nombre'       => 'Estado de Cuenta Comercio',
                    'descripcion'  => 'Emisión de Estado de Cuenta Comercio.',
                    'costo'        => 'Gratis',
                    'tiempo'       => '5 días hábiles',
                    'requisitos'   => 'Identificación, documento de la empresa.',
                    'documentacion' => 'Identificación, documento vigente.',
                    'pasos'        => '1. Solicitar el estado de cuenta. 2. Validar información. 3. Emitir permiso.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-file-invoice-dollar',
                ],
                [
                    'nombre'       => 'Convenio de Inmueble',
                    'descripcion'  => 'Convenio de Pago Inmueble.',
                    'costo'        => 'Gratis',
                    'tiempo'       => '10 días hábiles',
                    'requisitos'   => 'Documentación oficial, registro de empresa.',
                    'documentacion' => 'Registro comercial, identificación.',
                    'pasos'        => '1. Solicitar convenio. 2. Validar datos. 3. Emitir convenio.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-file-invoice',
                ],
                [
                    'nombre'       => 'Convenio de Comercio',
                    'descripcion'  => 'Convenio de Pago Comercio.',
                    'costo'        => 'Gratis',
                    'tiempo'       => '2 días hábiles',
                    'requisitos'   => 'Número de cuenta, identificación.',
                    'documentacion' => 'Número de cuenta, identificación oficial.',
                    'pasos'        => '1. Solicitar estado. 2. Validar información. 3. Emitir convenio.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-file-invoice',
                ],
                [
                    'nombre'       => 'Pago de Impuestos',
                    'descripcion'  => 'Pago de Impuestos y Tasas Municipales.',
                    'costo'        => 'Gratis',
                    'tiempo'       => '3 días hábiles',
                    'requisitos'   => 'Identificación, monto adeudado.',
                    'documentacion' => 'Copia de deuda, identificación.',
                    'pasos'        => '1. Solicitar plan. 2. Evaluar deuda. 3. Emitir plan.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-credit-card',
                ],
            ],
            'Registro Familiar' => [
                [
                    'nombre'       => 'Asentamiento de Nacimiento',
                    'descripcion'  => 'Inscripción de Nacimiento.',
                    'costo'        => 'Gratis',
                    'tiempo'       => '5 días hábiles',
                    'requisitos'   => 'Se debe informar en un plazo máximo de 45 días en base al art 51, Art 48 LREF.',
                    'documentacion' => '1. Ficha médica del nacimiento emitida por Sistema Nacional Integrado de Salud (PREREGISTRO NUI). 2. Copia de DUI de ambos padres si aplicare.',
                    'pasos'        => '1. La persona informante deberá demostrar la ocurrencia del nacimiento al registrador del estado familiar, mediante la declaración de un testigo mayor de dieciocho años que pueda dar fe conforme a lo dispuesto en esta ley. 2. No existen limitaciones en cuanto a que el testigo sea familiar de la madre o padre del recién nacido.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-baby',
                ],
                [
                    'nombre'       => 'Emisión de Auténtica de Partidas',
                    'descripcion'  => 'Autenticación oficial de las partidas.',
                    'costo'        => '$9.98',
                    'tiempo'       => '5 días hábiles',
                    'requisitos'   => 'Se deberá presentar prueba documental si se solicita la modificación del nombre o apellidos de uno de los padres del interesado.',
                    'documentacion' => 'Partida original, identificación.',
                    'pasos'        => '1. Indicar el tipo de firma requerida. 2. Pagar el costo del trámite. 3. Emitir auténtica.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-id-card',
                ],
                [
                    'nombre'       => 'Carné de Identificación Personal',
                    'descripcion'  => 'Emisión de carné de menoridad.',
                    'costo'        => '$5.00',
                    'tiempo'       => '5 días hábiles',
                    'requisitos'   => 'Debe estar acompañado por al menos uno de sus padres o representante legal. El menor debe tener entre 7 y 17 años.',
                    'documentacion' => 'Certificación de partida de nacimiento reciente. Debe residir dentro del municipio. Acompañarse con uno de sus padres o responsable.',
                    'pasos'        => '1. Acudir a la Alcaldía con el menor y la documentación requerida. 2. Presentar la partida de nacimiento y la copia del DUI del acompañante. 3. Pagar el trámite. 4. Esperar la emisión del carné.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-user',
                ],
                [
                    'nombre'       => 'Constancias de Estado Familiar',
                    'descripcion'  => 'Constancia de Inexistencia (CI), Constancia de Soltería (CS).',
                    'costo'        => '$3.68',
                    'tiempo'       => '5 días hábiles',
                    'requisitos'   => 'Identificación, solicitud.',
                    'documentacion' => '1. Solicitud con datos personales: nombre, lugar de nacimiento, fecha de nacimiento y nombre completo de los padres (CI). 2. Certificación de partida de nacimiento reciente y copia de DUI (CS).',
                    'pasos'        => '1. Presentar la solicitud con los datos personales. 2. Realizar el pago del trámite. 3. Recibir la constancia.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-id-badge',
                ],
                [
                    'nombre'       => 'Marginaciones y/o Reposiciones',
                    'descripcion'  => 'Marginaciones (MG), Reposiciones (RP).',
                    'costo'        => '$3.68',
                    'tiempo'       => '1 día hábil',
                    'requisitos'   => 'Solicitud escrita dirigida al Registro del Estado Familiar especificando el tipo de marginación solicitada (MG). Documentos probatorios (MG y RP).',
                    'documentacion' => '1. Documentos probatorios que respalden la modificación (MG). 2. Copia del DUI del solicitante (MG y RP). 3. Se realiza a solicitud de la parte interesada. 4. Llenado de formulario y solicitud (RP). 5. Partida certificada por Notario o Microfilm emitido por RNPN (RP).',
                    'pasos'        => '1. Presentarse a la unidad de Registro Familiar. 2. Pagar aranceles.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-receipt',
                ],
                [
                    'nombre'       => 'Certificaciones de Estado Familiar',
                    'descripcion'  => 'Certificación de Partidas de Nacimiento (CPN), Matrimonio (CM), Defunción (CPD), Unión No Matrimonial (CUNM), Divorcios Históricos (CDH), Cédula de Identidad (CCI).',
                    'costo'        => '$3.68',
                    'tiempo'       => '5 días hábiles',
                    'requisitos'   => 'a. Solicitud con datos personales: nombre, lugar y fecha de nacimiento (CPN). b. Solicitud con nombres completos de los contrayentes, fecha y lugar (CM). c. Solicitud con el nombre del fallecido, fecha y lugar del fallecimiento, nombre de los padres o cónyuge si aplica (CPD). d. Solicitud con los nombres de los convivientes, fecha y lugar de la unión (CUNM). e. Solicitud con nombres completos de las partes involucradas, fecha y lugar del divorcio (CDH). f. Solicitud con fecha exacta de emisión de cédula y nombre completo de los padres (CCI).',
                    'documentacion' => 'Copia del DUI del solicitante (CPN, CM, CPD, CDH). Copia del DUI de ambos convivientes (CUNM).',
                    'pasos'        => '1. Presentarse a la Alcaldía en unidad de registro familiar (REF). 2. Llenar la solicitud con los datos de la certificación requerida. 3. Pagar el arancel establecido. 4. Emitir certificación.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-certificate',
                ],
                [
                    'nombre'       => 'Asentamiento de Defunción',
                    'descripcion'  => 'Registro de defunción.',
                    'costo'        => '$3.68',
                    'tiempo'       => '5 días hábiles',
                    'requisitos'   => 'Cualquier pariente por consanguinidad o afinidad de un fallecido debe informarlo al registrador del estado familiar.',
                    'documentacion' => 'Esquela del Instituto de Medicina Legal, constancia médica o certificado firmado por el médico que atendió el cadáver.',
                    'pasos'        => '1. Si no es posible obtener constancia médica, el deceso debe probarse con autopsia o testigo.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-cross',
                ],
                [
                    'nombre'       => 'Inscripciones de Uniones No Matrimoniales',
                    'descripcion'  => 'Registro de unión no matrimonial.',
                    'costo'        => '$3.68',
                    'tiempo'       => '5 días hábiles',
                    'requisitos'   => 'Certificación de la sentencia que declara la existencia de la unión.',
                    'documentacion' => 'Certificación de Sentencia ejecutoriada por medio de oficio por el Juez competente.',
                    'pasos'        => '1. Presentarse a la Alcaldía con la documentación. 2. Llenar la solicitud. 3. Pagar el arancel establecido.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-handshake',
                ],
                [
                    'nombre'       => 'Inscripciones de Matrimonios',
                    'descripcion'  => 'Registro de matrimonio.',
                    'costo'        => '$3.68',
                    'tiempo'       => '5 días hábiles',
                    'requisitos'   => 'El funcionario autorizado deberá remitir al registrador del Estado Familiar la certificación del acta de celebración del matrimonio.',
                    'documentacion' => 'Testimonio de escritura pública de la celebración del matrimonio.',
                    'pasos'        => '1. Presentarse a la Alcaldía con la documentación. 2. Llenar la solicitud. 3. Pagar el arancel establecido.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-ring',
                ],
            ],
            'Inmuebles' => [
                [
                    'nombre'       => 'Inscripción de Inmueble',
                    'descripcion'  => 'Inscripción de un inmueble en el registro.',
                    'costo'        => 'Gratis',
                    'tiempo'       => '3 días hábiles',
                    'requisitos'   => 'El trámite debe ser personal. En caso contrario, el representante debe presentar un poder especial.',
                    'documentacion' => '1. Copia de escritura del inmueble. 2. Copia de DUI del propietario del inmueble.',
                    'pasos'        => '1. Presentar toda la documentación al departamento de registro tributario. 2. Esperar la notificación de la inspección para verificación de servicios recibidos en el inmueble. 3. Notificación del pago mensual de tasas por servicios municipales.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-home',
                ],
                [
                    'nombre'       => 'Solicitud para Inscripción de Empresas o Negocios',
                    'descripcion'  => 'Solicitud para inscribir empresas o negocios en el registro tributario municipal.',
                    'costo'        => 'Gratis',
                    'tiempo'       => '3 días hábiles',
                    'requisitos'   => 'El trámite debe ser personal. En caso contrario, el representante debe presentar un poder especial.',
                    'documentacion' => '1. Copia de escritura del inmueble. 2. Copia de DUI del propietario del inmueble. 3. Copia de escritura de constitución (si es asociación). 4. Balance inicial. 5. Declaración jurada. 6. Solvencia municipal. 7. Vialidad.',
                    'pasos'        => '1. Presentar toda la documentación al departamento de registro tributario. 2. Esperar la notificación de la determinación de pago de impuestos y firmar el documento. 3. Luego de la cancelación, retirar la constancia en el departamento de registro tributario.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-briefcase',
                ],
            ],
            'Trámites Cementerio' => [
                [
                    'nombre'       => 'Enterramiento',
                    'descripcion'  => 'Enterramiento en fábrica ínfima y prórroga de un año para conservar cadáver en la misma sepultura.',
                    'costo'        => '$8.46',
                    'tiempo'       => '4 días hábiles',
                    'requisitos'   => 'Fotocopia del DUI del fallecido.',
                    'documentacion' => 'Fotocopia del DUI del tramitante.',
                    'pasos'        => '1. Presentar ambas fotocopias de DUI en la unidad de cementerio. 2. Validación de datos. 3. Emitir certificación.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-skull',
                ],
                [
                    'nombre'       => 'Remodelación de Bóveda',
                    'descripcion'  => 'Proceso de renovación o reparación de una bóveda existente.',
                    'costo'        => 'Según presupuesto de remodelación',
                    'tiempo'       => '2 días hábiles',
                    'requisitos'   => 'Título de perpetuidad.',
                    'documentacion' => 'Fotocopia del DUI.',
                    'pasos'        => '1. Presentar solicitud. 2. Entregar fotocopia del DUI y número del título de perpetuidad en la unidad de cementerio. 3. Aprobación del permiso. 4. Inicio de remodelación.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-tools',
                ],
                [
                    'nombre'       => 'Construcción de Nichos Tamaño Estándar',
                    'descripcion'  => 'Solicitud para construir dos nichos en el cementerio.',
                    'costo'        => '$74.19',
                    'tiempo'       => '2 días hábiles',
                    'requisitos'   => 'Título de perpetuidad.',
                    'documentacion' => 'Entregar fotocopia del DUI en la unidad de cementerio.',
                    'pasos'        => '1. Presentar identificación. 2. Evaluación técnica. 3. Aprobación de construcción.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-hard-hat',
                ],
                [
                    'nombre'       => 'Derecho a Perpetuidad de Puesto',
                    'descripcion'  => 'Adquisición del derecho a perpetuidad por un puesto que mide 2.50×1.20 mts².',
                    'costo'        => '$47.94',
                    'tiempo'       => '1 día hábil',
                    'requisitos'   => 'Título de perpetuidad.',
                    'documentacion' => 'Copia de identificación DUI.',
                    'pasos'        => '1. Presentar identificación. 2. Validación del espacio disponible. 3. Firma del contrato.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-scroll',
                ],
                [
                    'nombre'       => 'Prórrogas de Sepultura',
                    'descripcion'  => 'Prórroga de 7 años para conservar cadáver en misma sepultura.',
                    'costo'        => '$15.75',
                    'tiempo'       => '2 días hábiles',
                    'requisitos'   => 'Título de perpetuidad, autorización de la familia.',
                    'documentacion' => 'Número del título de perpetuidad y último recibo de pago.',
                    'pasos'        => '1. Presentar solicitud. 2. Evaluación del espacio. 3. Firma de prórroga.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-clock',
                ],
                [
                    'nombre'       => 'Abrir y Cerrar Nicho',
                    'descripcion'  => 'Servicio de apertura y cierre de nicho en caso de exhumación o entierro.',
                    'costo'        => '$21.06',
                    'tiempo'       => '1 día hábil',
                    'requisitos'   => 'Autorización familiar, permiso del cementerio, título de perpetuidad.',
                    'documentacion' => 'Fotocopia del DUI.',
                    'pasos'        => '1. Presentar solicitud. 2. Entregar fotocopia del DUI y número del título de perpetuidad. 3. Programar apertura/cierre.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-door-open',
                ],
                [
                    'nombre'       => 'Extracción de Osamenta para Traslado',
                    'descripcion'  => 'Procedimiento para retirar restos óseos y trasladarlos a otro lugar.',
                    'costo'        => '$26.82',
                    'tiempo'       => '2 días hábiles',
                    'requisitos'   => 'Título de perpetuidad, partida de defunción, autorización del Ministerio de Salud, título de perpetuidad de destino de la osamenta.',
                    'documentacion' => 'Fotocopias de DUI del tramitante y del fallecido.',
                    'pasos'        => '1. Presentar solicitud. 2. Entregar toda la documentación en la unidad de cementerio. 3. Programar extracción y traslado.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-bone',
                ],
                [
                    'nombre'       => 'Carátula del Título de Perpetuidad',
                    'descripcion'  => 'Reemplazo o actualización de la carátula del título de perpetuidad.',
                    'costo'        => '$2.10',
                    'tiempo'       => '3 días hábiles',
                    'requisitos'   => 'Título de perpetuidad.',
                    'documentacion' => 'Copia del título de perpetuidad.',
                    'pasos'        => '1. Presentar título original. 2. Validación de datos. 3. Emisión de nueva carátula.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-file-alt',
                ],
                [
                    'nombre'       => 'Licencia para Construcción de Bóveda',
                    'descripcion'  => 'Permiso necesario para construir una bóveda en el cementerio.',
                    'costo'        => '$11.22',
                    'tiempo'       => '2 días hábiles',
                    'requisitos'   => 'Título de perpetuidad.',
                    'documentacion' => 'Fotocopia del DUI y número del título de perpetuidad.',
                    'pasos'        => '1. Presentar fotocopia del DUI y número del título de perpetuidad. 2. Aprobación del permiso.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-file-contract',
                ],
            ],
            'Trámites de Construcción' => [
                [
                    'nombre'       => 'Licencia Habitacional',
                    'descripcion'  => 'Licencia de construcción para viviendas habitacionales.',
                    'costo'        => 'Según el monto presupuestado',
                    'tiempo'       => '1 semana (siempre y cuando se presenten todos los requisitos el mismo día)',
                    'requisitos'   => 'Copia de escrituras del inmueble. Copia del DUI del propietario. Copia de NIT si es asociación o empresa.',
                    'documentacion' => 'Presupuesto real de lo invertido en la construcción. Copia de planos en físico y formato digital. Factibilidades requeridas.',
                    'pasos'        => '1. Presentar la documentación requerida. 2. Revisión por el técnico de la unidad. 3. Inspección por fiscalización municipal. 4. Elaboración de la licencia y cálculo. 5. Notificación del mandamiento de pago. 6. Recibir la licencia, presentando copia del recibo de cancelación.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-id-card',
                ],
                [
                    'nombre'       => 'Licencia Comercial',
                    'descripcion'  => 'Licencia de construcción para proyectos comerciales.',
                    'costo'        => 'Según el monto presupuestado',
                    'tiempo'       => '1 semana (siempre y cuando se presenten todos los requisitos el mismo día)',
                    'requisitos'   => 'Copia de escrituras del inmueble. Copia del DUI del propietario. Copia de NIT si es asociación o empresa.',
                    'documentacion' => 'Presupuesto real de lo invertido en la construcción. Copia de planos en físico y formato digital. Factibilidades requeridas.',
                    'pasos'        => '1. Presentar la documentación requerida. 2. Revisión por el técnico de la unidad. 3. Inspección por fiscalización municipal. 4. Elaboración de la licencia y cálculo. 5. Notificación del mandamiento de pago. 6. Recibir la licencia, presentando copia del recibo de cancelación.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-building',
                ],
            ],
            'Contravencional' => [
                [
                    'nombre'       => 'Procesos Administrativos Sancionatorios',
                    'descripcion'  => 'Denuncias, avisos y esquelas de desplazamiento impuestas por el CAM.',
                    'costo'        => 'Gratuito',
                    'tiempo'       => '1 mes - 6 meses',
                    'requisitos'   => 'Con la resolución final emitida por el Delegado Contravencional, quien mediante fundamentación legal garantiza los Derechos y garantías constitucionales de todos los ciudadanos, donde finalmente se absolverá o sancionará al infractor con amonestaciones verbales, reparación de daños, decomisos, trabajo de utilidad pública, multas, suspensiones de permisos y licencias y cierre definitivo de los negocios.',
                    'documentacion' => '1. Presentarse a la unidad con su Documento Único de Identidad o Pasaporte. 2. Las pruebas que presentan para probar y desvirtuar los hechos.',
                    'pasos'        => 'a. Que se sienta perjudicado por algún problema de convivencia ciudadana regulado en la Ordenanza de Convivencia Ciudadana y Contravencional de esta Municipalidad. b. Que proporcione información para poder emplazar al infractor. c. Que cite con exactitud los hechos ocurridos y en qué lugar. d. Que exista disponibilidad de tiempo para asistir a las audiencias y colaborar en todo lo que se le requiera.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-gavel',
                ],
            ],
            'Mercado' => [
                [
                    'nombre'       => 'Solvencia de Impuesto de los Mercados',
                    'descripcion'  => 'Obtención de constancia de solvencia de impuesto.',
                    'costo'        => '$3.00',
                    'tiempo'       => 'Máximo 10 minutos',
                    'requisitos'   => '1. Estar inscrito en el sistema de SIMERCO. 2. Estar al día con los pagos de los impuestos de los mercados.',
                    'documentacion' => '1. Presentar el DUI. 2. Presentar nombre de locatario del puesto.',
                    'pasos'        => 'Presentarse en la Unidad de Mercados.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-file-alt',
                ],
                [
                    'nombre'       => 'Planes de Pago de Puestos en Mora',
                    'descripcion'  => 'Opción para regularizar deudas de los puestos.',
                    'costo'        => 'Gratis',
                    'tiempo'       => 'Máximo 10 minutos',
                    'requisitos'   => '1. Estar inscrito en el sistema de SIMERCO. 2. Cancelar el 30% de la deuda total.',
                    'documentacion' => '1. Presentar el DUI. 2. Presentar nombre de locatario del puesto. 3. Estado de cuentas.',
                    'pasos'        => 'Presentarse en la Unidad de Mercados.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-credit-card',
                ],
                [
                    'nombre'       => 'Estado de Cuentas de Mercados',
                    'descripcion'  => 'Verificación del estado de cuentas de los puestos.',
                    'costo'        => 'Gratis',
                    'tiempo'       => 'Máximo 10 minutos',
                    'requisitos'   => '1. Estar inscrito en el sistema de SIMERCO.',
                    'documentacion' => '1. Presentar el DUI. 2. Presentar nombre de locatario del puesto.',
                    'pasos'        => 'Presentarse en la Unidad de Mercados.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-file-invoice',
                ],
                [
                    'nombre'       => 'Pago de Impuestos de los Mercados Municipales',
                    'descripcion'  => 'Pago de impuestos correspondientes a los mercados.',
                    'costo'        => 'Lo define su tipo de impuesto',
                    'tiempo'       => 'Máximo 10 minutos',
                    'requisitos'   => '1. Estar inscrito en el sistema de SIMERCO. 2. Estar al día con los impuestos.',
                    'documentacion' => '1. Presentar el DUI. 2. Presentar tarjeta de cobro al día. 3. Presentar nombre de locatario del puesto.',
                    'pasos'        => 'Presentarse en la Unidad de Mercados.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-money-bill',
                ],
                [
                    'nombre'       => 'Trámites de Licencia de Comercialización',
                    'descripcion'  => 'Obtención o renovación de licencias de comercialización.',
                    'costo'        => '$5.25, $10.50, $12.60 (dependiendo del trámite)',
                    'tiempo'       => 'Máximo 10 minutos',
                    'requisitos'   => '1. Estar inscrito en el sistema de SIMERCO. 2. Cancelar la licencia.',
                    'documentacion' => '1. Presentar el DUI. 2. Presentar recibo de cancelación de la licencia. 3. Presentar nombre de locatario del puesto.',
                    'pasos'        => 'Presentarse en la Unidad de Mercados.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-id-card',
                ],
                [
                    'nombre'       => 'Autorización de Remodelación de Puesto',
                    'descripcion'  => 'Solicitud para remodelar el puesto.',
                    'costo'        => 'Gratis',
                    'tiempo'       => 'Máximo 10 minutos',
                    'requisitos'   => '1. Estar inscrito en el sistema de SIMERCO. 2. Estar al día con sus impuestos de los mercados municipales.',
                    'documentacion' => '1. Presentar el DUI. 2. Presentar tarjeta de cobro al día. 3. Presentar nombre de locatario del puesto.',
                    'pasos'        => 'Presentarse en la Unidad de Mercados.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-tools',
                ],
                [
                    'nombre'       => 'Constancia de Puesto',
                    'descripcion'  => 'Obtención de una constancia que acredita la ocupación del puesto.',
                    'costo'        => '$3.00',
                    'tiempo'       => 'Máximo 10 minutos',
                    'requisitos'   => '1. Estar inscrito en el sistema de SIMERCO. 2. Estar al día con los pagos de los impuestos de los mercados.',
                    'documentacion' => '1. Presentar el DUI. 2. Presentar nombre de locatario del puesto.',
                    'pasos'        => 'Presentarse en la Unidad de Mercados.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-certificate',
                ],
            ],
            'Visto Bueno' => [
                [
                    'nombre'       => 'Matrículas',
                    'descripcion'  => 'Elaboración de Cartas de Venta.',
                    'costo'        => '$3.77 carta de venta',
                    'tiempo'       => '10 a 15 minutos',
                    'requisitos'   => 'Reglamento para el uso de fierros o marcas de herrar ganado y traslado de semovientes art. 73 y ordenanza municipal.',
                    'documentacion' => '1. DUI de comprador vigente. 2. DUI de vendedor vigente. 3. Matrícula de fierro vigente. 4. Partida de nacimiento del comprador (para acta de traspaso). 5. 2 fotografías.',
                    'pasos'        => '1. Presentar los documentos requeridos. 2. Esperar la revisión de los documentos. 3. Recibir la elaboración del documento solicitado. 4. Recibir el mandamiento y/o recibo de cancelación. 5. Firmar junto a la alcaldesa y el secretario o secretaria municipal. 6. Recibir el documento final.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-file-alt',
                ],
                [
                    'nombre'       => 'Actas de Traspaso',
                    'descripcion'  => 'Elaboración de acta de traspaso de matrícula de fierro.',
                    'costo'        => '$21.17',
                    'tiempo'       => '10 a 15 minutos',
                    'requisitos'   => 'Reglamento para el uso de fierros o marcas de herrar ganado y traslado de semovientes art. 73 y ordenanza municipal.',
                    'documentacion' => '1. DUI de comprador vigente. 2. DUI de vendedor vigente. 3. Matrícula de fierro vigente. 4. Partida de nacimiento del comprador. 5. 2 fotografías.',
                    'pasos'        => '1. Presentar los documentos requeridos. 2. Esperar la revisión de los documentos. 3. Recibir la elaboración del acta de traspaso. 4. Firmar junto a la alcaldesa y el secretario o secretaria municipal. 5. Recibir el documento final.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-file-alt',
                ],
                [
                    'nombre'       => 'Guías de Conducción',
                    'descripcion'  => 'Elaboración de guías de conducción de ganado a otras jurisdicciones.',
                    'costo'        => '$2.15 por cabeza',
                    'tiempo'       => '10 a 15 minutos',
                    'requisitos'   => 'De conformidad al artículo 73 del Reglamento para el uso de fierro o marcas de herrar ganado y traslado de semovientes.',
                    'documentacion' => '1. DUI vigente del contribuyente que solicita la guía. 2. DUI del conductor. 3. Matrícula de fierro del contribuyente. 4. Tarjeta de circulación.',
                    'pasos'        => '1. Presentar los documentos requeridos. 2. Esperar la revisión de los documentos. 3. Recibir la elaboración del documento solicitado. 4. Recibir el mandamiento o recibo de pago. 5. Firmar junto a la alcaldesa y el secretario municipal. 6. Recibir el documento final.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-truck',
                ],
                [
                    'nombre'       => 'Cobros de Matrículas',
                    'descripcion'  => 'Cobro de matrícula de fierro de herrar ganado.',
                    'costo'        => '$21.17',
                    'tiempo'       => '10 a 15 minutos',
                    'requisitos'   => 'Ordenanza de tasas municipales, art. 21, publicada en el diario oficial Tomo No 424, página 70, número 135, fecha 22 de julio de 2019.',
                    'documentacion' => 'Matrícula de fierro.',
                    'pasos'        => '1. Presentar la matrícula de fierro. 2. Esperar la revisión de la matrícula. 3. Verificar en el libro de anotaciones. 4. Recibir el mandamiento o recibo. 5. Pagar en caja. 6. Recibir el recibo final.',
                    'horario'      => 'Lunes a Viernes 8 AM - 4 PM',
                    'icono'        => 'fa-solid fa-money-bill',
                ],
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DATA — SERVICIOS MUNICIPALES
    // ─────────────────────────────────────────────────────────────────────────
    private function serviciosCatalog(): array
    {
        return [
            'Salud' => [
                [
                    'nombre'      => 'Atención Médica Primaria',
                    'descripcion' => 'Clínicas municipales que ofrecen atención básica y programas de salud preventiva.',
                    'icono'       => 'fa-solid fa-stethoscope',
                ],
                [
                    'nombre'      => 'Control de Plagas',
                    'descripcion' => 'Control de plagas en espacios públicos para garantizar la salubridad del municipio.',
                    'icono'       => 'fa-solid fa-bug',
                ],
                [
                    'nombre'      => 'Campañas de Vacunación',
                    'descripcion' => 'Programas de vacunación para prevenir enfermedades en la comunidad.',
                    'icono'       => 'fa-solid fa-syringe',
                ],
            ],
            'Infraestructura' => [
                [
                    'nombre'      => 'Alumbrado Público',
                    'descripcion' => 'Instalación y mantenimiento de alumbrado en calles del municipio.',
                    'icono'       => 'fa-solid fa-lightbulb',
                ],
                [
                    'nombre'      => 'Mantenimiento de Parques y Jardines',
                    'descripcion' => 'Cuidado de parques y áreas verdes municipales.',
                    'icono'       => 'fa-solid fa-tree',
                ],
                [
                    'nombre'      => 'Reparación de Calles',
                    'descripcion' => 'Mantenimiento y reparación de calles municipales.',
                    'icono'       => 'fa-solid fa-road',
                ],
            ],
            'Asistencia Social' => [
                [
                    'nombre'      => 'Asistencia a Familias Vulnerables',
                    'descripcion' => 'Apoyo a familias en condiciones de pobreza extrema.',
                    'icono'       => 'fa-solid fa-hands-helping',
                ],
                [
                    'nombre'      => 'Ayuda para Personas con Discapacidad',
                    'descripcion' => 'Programa de apoyo para personas con discapacidad.',
                    'icono'       => 'fa-solid fa-wheelchair',
                ],
                [
                    'nombre'      => 'Atención a Adultos Mayores',
                    'descripcion' => 'Apoyo integral a la tercera edad.',
                    'icono'       => 'fa-solid fa-user-friends',
                ],
            ],
        ];
    }
}
