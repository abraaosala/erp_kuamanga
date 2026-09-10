<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Vencimento</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1a202c; margin: 0; padding: 28px 32px; }
        table { border-collapse: collapse; width: 100%; }

        /* ---- Topo ---- */
        .top { width: 100%; border-bottom: 3px solid #0f766e; padding-bottom: 14px; margin-bottom: 18px; }
        .top-table { width: 100%; }
        .top-logo { width: 132px; height: 88px; padding-right: 14px; vertical-align: middle; }
        .top-logo .logo-box { width: 118px; height: 78px; border: 1px dashed #cbd5e1; border-radius: 6px; text-align: center; color: #94a3b8; font-size: 9px; line-height: 78px; }
        .top-empresa { vertical-align: middle; }
        .top-empresa h1 { font-size: 19px; margin: 0 0 3px; text-transform: uppercase; letter-spacing: .5px; color: #0f172a; }
        .top-empresa .nif { font-size: 10px; color: #475569; margin: 0 0 2px; font-weight: bold; }
        .top-empresa .endereco { font-size: 10px; color: #475569; margin: 0; line-height: 1.5; }
        .top-doc { vertical-align: middle; text-align: right; }
        .top-doc .badge { display: inline-block; background: #0f766e; color: #fff; font-size: 12px; font-weight: bold; padding: 6px 18px; border-radius: 4px; letter-spacing: 1px; margin-bottom: 10px; text-transform: uppercase; }
        .top-doc .campos { font-size: 10px; color: #334155; line-height: 1.7; }
        .top-doc .campos strong { color: #0f172a; font-weight: bold; }

        /* ---- Caixas de informação ---- */
        .info-box { width: 100%; }
        .info-box td { vertical-align: top; }
        .info-card { border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; }
        .info-card h3 { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #0f766e; margin: 0 0 6px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        .info-card .row { font-size: 10.5px; color: #334155; padding: 1.5px 0; }
        .info-card .row .label { color: #64748b; }
        .info-card .row strong { color: #0f172a; }

        /* ---- Resumo numérico ---- */
        .resumo { width: 100%; margin: 16px 0 14px; }
        .resumo td { width: 33.33%; }
        .resumo .box { border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px; background: #f8fafc; }
        .resumo .box .titulo { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 2px; }
        .resumo .box .valor { font-size: 17px; font-weight: bold; color: #0f172a; }
        .resumo td.spacer { padding: 0 0 0 0; }
        .resumo tr td + td { padding-left: 10px; }
        .resumo .liquido .box { background: #0f766e; border-color: #0f766e; }
        .resumo .liquido .box .titulo { color: #99f6e4; }
        .resumo .liquido .box .valor { color: #ffffff; }

        /* ---- Tabela de rubricas ---- */
        .rubricas { width: 100%; margin: 8px 0; }
        .rubricas th { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #fff; background: #334155; padding: 6px 10px; text-align: left; }
        .rubricas th.num, .rubricas td.num { text-align: right; }
        .rubricas td { font-size: 10.5px; padding: 5px 10px; border: 1px solid #e2e8f0; color: #334155; }
        .rubricas tr.secao td { background: #f1f5f9; font-weight: bold; color: #0f172a; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; padding-top: 7px; padding-bottom: 7px; }
        .rubricas tr.subtotal td { font-weight: bold; color: #0f172a; background: #f8fafc; }
        .rubricas tr.desc td { color: #b91c1c; }
        .rubricas td.bruto { font-weight: bold; color: #0f172a; }

        /* ---- Por extenso ---- */
        .extenso { border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; margin: 12px 0 4px; background: #f8fafc; }
        .extenso .titulo { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 3px; }
        .extenso .valor { font-size: 11.5px; color: #0f172a; font-style: italic; line-height: 1.5; }

        /* ---- Assinaturas ---- */
        .assinaturas { width: 100%; margin-top: 46px; }
        .assinaturas td { width: 50%; text-align: center; vertical-align: bottom; }
        .assinaturas .linha { border-top: 1px solid #94a3b8; padding-top: 6px; font-size: 10px; color: #334155; }
        .assinaturas .nome { font-weight: bold; font-size: 10.5px; color: #0f172a; margin-bottom: 2px; }
        .assinaturas td + td { padding-left: 40px; }

        /* ---- Rodapé ---- */
        .footer { margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 8px; text-align: center; font-size: 8.5px; color: #94a3b8; line-height: 1.6; }
    </style>
</head>
<body>
    @php
        $empresa = current_empresa();
        $date = date('d/m/Y H:i');
        $periodoStart = $payslip->payrollRun->period_start->format('d/m/Y');
        $periodoEnd   = $payslip->payrollRun->period_end->format('d/m/Y');
        $totalDescontos = (float) $payslip->absent_deduction + (float) $payslip->social_security + (float) $payslip->irt_amount;
    @endphp

    {{-- ==== Topo: empresa + documento ==== --}}
    <div class="top">
        <table class="top-table">
            <tr>
                <td class="top-logo">
                    @if ($empresa->logo && is_file(storage_uploads_path((string) $empresa->logo)))
                        <img src="{{ storage_uploads_path((string) $empresa->logo) }}" style="max-width:118px; max-height:78px; object-fit: contain;">
                    @else
                        <div class="logo-box">S/ LOGO</div>
                    @endif
                </td>
                <td class="top-empresa">
                    <h1>{{ $empresa->nome ?? '—' }}</h1>
                    @if (!empty($empresa->nif))
                        <p class="nif">NIF: {{ $empresa->nif }}</p>
                    @endif
                    <p class="endereco">{{ trim(trim((string)($empresa->morada ?? '') . ' ' . (string)($empresa->cidade ?? ''))) }}</p>
                </td>
                <td class="top-doc">
                    <span class="badge">Recibo de Vencimento</span>
                    <div class="campos">
                        <strong>Período:</strong> {{ $periodoStart }} a {{ $periodoEnd }}
                        <br><strong>Folha nº:</strong> {{ $payslip->payroll_run_id }}
                        <br><strong>Referência:</strong> {{ strtoupper('REC-' . $payslip->payroll_run_id . '-' . ($payslip->employee->id ?? '') . '-' . $payslip->id) }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ==== Informação do funcionário ==== --}}
    <table class="info-box">
        <tr>
            <td style="width:50%; padding-right:10px;">
                <div class="info-card">
                    <h3>Funcionário</h3>
                    <div class="row"><span class="label">Nome:</span> <strong>{{ $payslip->employee->name ?? '—' }}</strong></div>
                    <div class="row"><span class="label">Matrícula:</span> {{ $payslip->employee->id ?? '—' }}</div>
                    <div class="row"><span class="label">Cargo:</span> {{ $payslip->employee->position->name ?? '—' }}</div>
                    <div class="row"><span class="label">Departamento:</span> {{ $payslip->employee->department->name ?? '—' }}</div>
                </div>
            </td>
            <td style="width:50%; padding-left:10px;">
                <div class="info-card">
                    <h3>Identificação</h3>
                    <div class="row"><span class="label">BI:</span> {{ $payslip->employee->bi ?? '—' }}</div>
                    <div class="row"><span class="label">Nº INSS:</span> {{ $payslip->employee->inss ?? '—' }}</div>
                    <div class="row"><span class="label">Data de admissão:</span> {{ $payslip->employee->hire_date ? $payslip->employee->hire_date->format('d/m/Y') : '—' }}</div>
                    <div class="row"><span class="label">Emissão:</span> {{ $date }}</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ==== Resumo dos valores ==== --}}
    <table class="resumo">
        <tr>
            <td>
                <div class="box">
                    <div class="titulo">Vencimento Bruto</div>
                    <div class="valor">{{ number_format((float) $payslip->gross_salary, 2, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="box">
                    <div class="titulo">Total de Descontos</div>
                    <div class="valor">{{ number_format($totalDescontos, 2, ',', '.') }}</div>
                </div>
            </td>
            <td class="liquido">
                <div class="box">
                    <div class="titulo">Vencimento Líquido</div>
                    <div class="valor">{{ number_format((float) $payslip->net_salary, 2, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ==== Rubricas detalhadas ==== --}}
    <table class="rubricas">
        <thead>
            <tr>
                <th style="width:70%;">Rubrica</th>
                <th class="num" style="width:30%;">Valor (AOA)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="secao"><td colspan="2">Rendimentos</td></tr>
            <tr>
                <td>Salário Base</td>
                <td class="num">{{ number_format((float) $payslip->base_salary, 2, ',', '.') }}</td>
            </tr>
            @if ((float) $payslip->overtime_amount > 0)
                <tr>
                    <td>Horas Extra ({{ number_format((float) $payslip->overtime_hours, 2, ',', '.') }} h)</td>
                    <td class="num">{{ number_format((float) $payslip->overtime_amount, 2, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="subtotal">
                <td>Subtotal — Rendimentos</td>
                <td class="num">{{ number_format((float) $payslip->gross_salary, 2, ',', '.') }}</td>
            </tr>

            <tr class="secao"><td colspan="2">Descontos</td></tr>
            @if ((float) $payslip->absent_deduction > 0)
                <tr class="desc">
                    <td>Faltas ({{ number_format((float) $payslip->absent_days, 2, ',', '.') }} dia(s))</td>
                    <td class="num">-{{ number_format((float) $payslip->absent_deduction, 2, ',', '.') }}</td>
                </tr>
            @endif
            @if ((float) $payslip->social_security > 0)
                <tr class="desc">
                    <td>Segurança Social (3%)</td>
                    <td class="num">-{{ number_format((float) $payslip->social_security, 2, ',', '.') }}</td>
                </tr>
            @endif
            @if ((float) $payslip->irt_amount > 0)
                <tr class="desc">
                    <td>IRT (1ª parcela)</td>
                    <td class="num">-{{ number_format((float) $payslip->irt_amount, 2, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="subtotal">
                <td>Subtotal — Descontos</td>
                <td class="num">-{{ number_format($totalDescontos, 2, ',', '.') }}</td>
            </tr>

            <tr class="secao" style="background:#0f766e; color:#fff;">
                <td>Vencimento Líquido a Receber</td>
                <td class="num">{{ number_format((float) $payslip->net_salary, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ==== Por extenso ==== --}}
    <div class="extenso">
        <div class="titulo">Valor por extenso</div>
        <div class="valor">{{ $valorExtenso }}</div>
    </div>

    {{-- ==== Assinaturas ==== --}}
    <table class="assinaturas">
        <tr>
            <td>
                <div class="nome">{{ $payslip->employee->name ?? '—' }}</div>
                <div class="linha">Funcionário</div>
            </td>
            <td>
                <div class="nome">{{ $empresa->nome ?? '—' }}</div>
                <div class="linha">Entidade Empregadora</div>
            </td>
        </tr>
    </table>

    {{-- ==== Rodapé ==== --}}
    <div class="footer">
        Documento gerado eletronicamente pelo Kuamanga ERP em {{ $date }}<br>
        Este recibo confirma o pagamento do vencimento líquido ao funcionário acima identificado.
    </div>
</body>
</html>