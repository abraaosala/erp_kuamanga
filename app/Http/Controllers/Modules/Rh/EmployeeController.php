<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Services\Contracts\ContractServiceInterface;
use App\Services\Contracts\DepartmentServiceInterface;
use App\Services\Contracts\EmployeeDocumentServiceInterface;
use App\Services\Contracts\EmployeeScheduleServiceInterface;
use App\Services\Contracts\EmployeeServiceInterface;
use App\Services\Contracts\HourBankEntryServiceInterface;
use App\Services\Contracts\PositionServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Factory as Validator;

class EmployeeController
{
    public function __construct(
        protected EmployeeServiceInterface $employeeService,
        protected DepartmentServiceInterface $departmentService,
        protected PositionServiceInterface $positionService,
        protected EmployeeDocumentServiceInterface $employeeDocumentService,
        protected ContractServiceInterface $contractService,
        protected EmployeeScheduleServiceInterface $employeeScheduleService,
        protected HourBankEntryServiceInterface $hourBankEntryService,
        protected BladeOne $blade,
        protected Validator $validator
    ) {}

    public function index(Request $request): Response
    {
        $perPageRaw = $request->get('perPage', 15);
        /** @var int $perPage */
        $perPage    = is_numeric($perPageRaw) ? (int) $perPageRaw : 15;
        $searchRaw  = $request->get('search');
        /** @var string|null $search */
        $search     = is_string($searchRaw) ? $searchRaw : null;
        $employees  = $this->employeeService->paginate($perPage, $search);
        $html = $this->blade->run('rh.employees.index', [
            'employees' => $employees,
            'search'    => $search,
            'perPage'   => $perPage,
            'success'   => $_SESSION['flash_success'] ?? null,
            'error'     => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function create(Request $request): Response
    {
        $departments = $this->departmentService->getAll();
        $positions   = $this->positionService->getAll();

        $html = $this->blade->run('rh.employees.create', [
            'departments' => $departments,
            'positions'   => $positions,
            'error'       => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'name'          => 'required|min:2|max:150',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|max:17',
            'department_id' => 'nullable|integer|exists:departments,id',
            'position_id'   => 'nullable|integer|exists:positions,id',
            'hire_date'     => 'nullable|date',
            'bi'            => 'nullable|regex:/^[0-9]{9}[A-Za-z]{2}[0-9]{3}$/',
            'inss'          => 'nullable|digits_between:6,12',
        ], [
            'name.required'          => 'O nome do funcionário é obrigatório.',
            'name.min'               => 'O nome deve ter pelo menos 2 caracteres.',
            'email.email'            => 'Email inválido.',
            'hire_date.date'         => 'Data de admissão inválida.',
            'department_id.exists'   => 'Departamento selecionado não existe.',
            'position_id.exists'     => 'Cargo selecionado não existe.',
            'bi.regex'               => 'Formato de BI inválido (9 dígitos + 2 letras + 3 dígitos).',
            'inss.digits_between'    => 'O INSS deve conter apenas números (6 a 12 dígitos).',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/employees/create');
        }

        $docs = $request->input('docs', []);
        $docFiles = $request->file('document_files', []);
        unset($data['docs']);

        try {
            /** @var \App\Models\Employee $employee */
            $employee = $this->employeeService->create($data);

            if (is_array($docFiles)) {
                foreach ($docFiles as $index => $file) {
                    $doc = is_array($docs) ? ($docs[$index] ?? null) : null;
                    if (!is_array($doc)) {
                        continue;
                    }
                    $type = $doc['document_type'] ?? null;
                    $number = $doc['document_number'] ?? null;

                    $this->processDocumentUpload((int) $employee->id, $type, $number, $file);
                }
            }

            $_SESSION['flash_success'] = 'Funcionário cadastrado com sucesso!';
            return redirect('/rh/employees');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/employees/create');
        }
    }

    private function processDocumentUpload(int $employeeId, mixed $type, mixed $number, \Illuminate\Http\UploadedFile $file): bool
    {
        $typeString = is_string($type) ? $type : '';
        $allowedTypes = [
            'bi', 'inss', 'contract', 'medical', 'certificate', 'cv', 'photo',
        ];
        if (!in_array($typeString, $allowedTypes, true)) {
            $typeString = 'bi';
        }

        if (!$file->isValid()) {
            return false;
        }

        if ($file->getSize() > 2097152) {
            $_SESSION['flash_error'] = 'O ficheiro excede o tamanho máximo de 2MB.';
            return false;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();
        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
        ];

        if (!is_string($mime) || !isset($mimeTypes[$extension]) || $mime !== $mimeTypes[$extension]) {
            $_SESSION['flash_error'] = 'Tipo de ficheiro não permitido. Use PDF, JPG ou PNG.';
            return false;
        }

        try {
            $originalName = $file->getClientOriginalName();
            $size = $file->getSize();
            $numberString = is_string($number) ? $number : '';
            $relativePath = upload_file($file, 'employees/' . $employeeId);

            $this->employeeDocumentService->create([
                'employee_id'     => $employeeId,
                'document_type'   => $typeString,
                'document_number' => $numberString !== '' ? $numberString : null,
                'file_path'       => $relativePath,
                'file_name'       => $originalName,
                'file_size'       => $size,
                'mime_type'       => $mime,
            ]);
            return true;
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Não foi possível guardar o documento.';
            return false;
        }
    }

    public function edit(Request $request, int $id): Response|RedirectResponse
    {
        $employee = $this->employeeService->getById($id);


        if (!$employee) {
            $_SESSION['flash_error'] = 'Funcionário não encontrado.';
            return redirect('/rh/employees');
        }

        $departments = $this->departmentService->getAll();
        $positions   = $this->positionService->getAll();
        $documents   = $this->employeeDocumentService->getByEmployee((int) $employee->id);

        $html = $this->blade->run('rh.employees.edit', [
            'employee'    => $employee,
            'departments' => $departments,
            'positions'   => $positions,
            'documents'   => $documents,
            'error'       => $_SESSION['flash_error'] ?? null,
            'success'     => $_SESSION['flash_success'] ?? null,
        ]);
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return response($html);
    }

    public function show(Request $request, int $id): Response|RedirectResponse
    {
        $employee = $this->employeeService->getById($id);
        if (!$employee) {
            $_SESSION['flash_error'] = 'Funcionário não encontrado.';
            return redirect('/rh/employees');
        }

        $contracts = $this->contractService->getByEmployee((int) $employee->id);
        $documents = $this->employeeDocumentService->getByEmployee((int) $employee->id);
        $schedules = $this->employeeScheduleService->getSchedulesByEmployee((int) $employee->id);
        $balance   = $this->hourBankEntryService->balanceByEmployee((int) $employee->id);

        $html = $this->blade->run('rh.employees.show', [
            'employee'    => $employee,
            'contracts'   => $contracts,
            'documents'   => $documents,
            'schedules'   => $schedules,
            'balance'     => $balance,
            'error'       => $_SESSION['flash_error'] ?? null,
            'success'     => $_SESSION['flash_success'] ?? null,
        ]);
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return response($html);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->all();
        $validation = $this->validator->make($data, [
            'name'          => 'required|min:2|max:150',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|max:17',
            'department_id' => 'nullable|integer|exists:departments,id',
            'position_id'   => 'nullable|integer|exists:positions,id',
            'hire_date'     => 'nullable|date',
            'bi'            => 'nullable|regex:/^[0-9]{9}[A-Za-z]{2}[0-9]{3}$/',
            'inss'          => 'nullable|digits_between:6,12',
        ], [
            'name.required'          => 'O nome do funcionário é obrigatório.',
            'name.min'               => 'O nome deve ter pelo menos 2 caracteres.',
            'email.email'            => 'Email inválido.',
            'hire_date.date'         => 'Data de admissão inválida.',
            'department_id.exists'   => 'Departamento selecionado não existe.',
            'position_id.exists'     => 'Cargo selecionado não existe.',
            'bi.regex'               => 'Formato de BI inválido (9 dígitos + 2 letras + 3 dígitos).',
            'inss.digits_between'    => 'O INSS deve conter apenas números (6 a 12 dígitos).',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/employees/' . $id . '/edit');
        }

        try {
            $this->employeeService->update($id, $data);
            $_SESSION['flash_success'] = 'Funcionário atualizado com sucesso!';
            return redirect('/rh/employees');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/employees/' . $id . '/edit');
        }
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        try {
            $this->employeeService->delete($id);
            $_SESSION['flash_success'] = 'Funcionário removido com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/employees');
    }
}
