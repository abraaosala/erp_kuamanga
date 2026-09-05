<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Services\Contracts\EmployeeDocumentServiceInterface;
use App\Services\Contracts\EmployeeServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Factory as Validator;

class EmployeeDocumentController
{
    protected const MAX_SIZE = 2097152; // 2MB

    /** @var array<string, string> */
    protected const MIME_TYPES = [
        'pdf'  => 'application/pdf',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
    ];

    /** @var array<string, string> */
    protected const TYPE_LABELS = [
        'bi'          => 'BI',
        'inss'        => 'INSS',
        'contract'    => 'Contrato',
        'medical'     => 'Atestado médico',
        'certificate' => 'Certificado',
        'cv'          => 'CV',
    ];

    public function __construct(
        protected EmployeeDocumentServiceInterface $employeeDocumentService,
        protected EmployeeServiceInterface $employeeService,
        protected Validator $validator
    ) {}

    public function store(Request $request, int $employeeId): RedirectResponse
    {
        $employee = $this->employeeService->getById($employeeId);
        if (!$employee) {
            $_SESSION['flash_error'] = 'Funcionário não encontrado.';
            return redirect('/rh/employees');
        }

        $docs = $request->input('docs', []);
        $docFiles = $request->file('document_files', []);

        if (!is_array($docFiles) || count($docFiles) === 0 || !is_array($docs)) {
            $_SESSION['flash_error'] = 'Seleccione pelo menos um ficheiro para enviar.';
            return redirect('/rh/employees/' . $employeeId . '/edit');
        }

        $saved = 0;
        $lastError = null;
        foreach ($docFiles as $index => $file) {
            $doc = $docs[$index] ?? null;
            if (!is_array($doc)) {
                continue;
            }

            $type = $doc['document_type'] ?? null;
            $documentNumber = $doc['document_number'] ?? null;

            if (!is_string($type) || !array_key_exists($type, self::TYPE_LABELS)) {
                continue;
            }

            if (($error = $this->validateFile($file)) !== null) {
                $lastError = $error;
                continue;
            }

            try {
                $mime = $file->getMimeType();
                $originalName = $file->getClientOriginalName();
                $size = $file->getSize();
                $relativePath = upload_file($file, 'employees/' . $employeeId);

                $this->employeeDocumentService->create([
                    'employee_id'     => $employeeId,
                    'document_type'   => $type,
                    'document_number' => is_string($documentNumber) && $documentNumber !== '' ? $documentNumber : null,
                    'file_path'       => $relativePath,
                    'file_name'       => $originalName,
                    'file_size'       => $size,
                    'mime_type'       => is_string($mime) ? $mime : '',
                ]);
                $saved++;
            } catch (\Exception $e) {
                $lastError = 'Não foi possível guardar o documento: ' . $e->getMessage();
            }
        }

        if ($saved === 0) {
            $_SESSION['flash_error'] = $lastError ?: 'Nenhum documento pôde ser guardado. Verifique o tipo/ficha do ficheiro.';
        } else {
            $_SESSION['flash_success'] = $saved . ' documento(s) adicionado(s) com sucesso!';
        }

        return redirect('/rh/employees/' . $employeeId . '/edit');
    }

    private function validateFile(\Illuminate\Http\UploadedFile $file): ?string
    {
        if (!$file->isValid()) {
            return 'O ficheiro enviado está inválido (erro de upload).';
        }

        if ($file->getSize() > self::MAX_SIZE) {
            return 'O ficheiro excede o tamanho máximo de 2MB.';
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();

        if (!isset(self::MIME_TYPES[$extension])) {
            return 'Extensão .' . $extension . ' não permitida. Use PDF, JPG ou PNG.';
        }

        $allowedMime = self::MIME_TYPES[$extension];
        if (!is_string($mime) || $mime !== $allowedMime) {
            return 'Tipo do ficheiro não corresponde à extensão .' . $extension . ' (detectado: ' . (is_string($mime) ? $mime : 'desconhecido') . ').';
        }

        return null;
    }

    public function download(Request $request, int $employeeId, int $docId): Response
    {
        $employee = $this->employeeService->getById($employeeId);
        if (!$employee) {
            $_SESSION['flash_error'] = 'Funcionário não encontrado.';
            return response('Funcionário não encontrado.', 404);
        }

        $doc = $this->employeeDocumentService->getById($docId);
        if (!$doc || $doc->employee_id !== $employeeId) {
            $_SESSION['flash_error'] = 'Documento não encontrado.';
            return response('Documento não encontrado.', 404);
        }

        return download_file((string) $doc->file_path, (string) $doc->file_name, $request->boolean('inline'));
    }

    public function destroy(Request $request, int $employeeId, int $docId): RedirectResponse
    {
        $doc = $this->employeeDocumentService->getById($docId);

        if ($doc && $doc->employee_id === $employeeId) {
            $full = storage_uploads_path((string) $doc->file_path);
            if (is_file($full)) {
                @unlink($full);
            }
            $this->employeeDocumentService->delete((int) $doc->id);
            $_SESSION['flash_success'] = 'Documento removido com sucesso!';
        } else {
            $_SESSION['flash_error'] = 'Documento não encontrado.';
        }

        return redirect('/rh/employees/' . $employeeId . '/edit');
    }
}
