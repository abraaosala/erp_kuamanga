<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Regra de integridade cargo ↔ departamento.
 *
 * Como Position pertence a Department (positions.department_id), qualquer
 * entidade que guarde department_id + position_id tem de ser coerente:
 * o departamento, quando presente, tem de ser o do cargo escolhido.
 */
final class DepartmentPositionRule
{
    /**
     * Resolve o department_id coerente com o cargo dado.
     *
     * @param int|null $departmentId         departamento proposto (da entidade)
     * @param int|null $positionDepartmentId departamento a que o cargo pertence
     * @param bool     $derive               se deve preencher o departamento vazio a partir do cargo
     *
     * @return int|null departamento coerente a gravar
     *
     * @throws \InvalidArgumentException quando ambos existem e divergem
     */
    public static function resolve(?int $departmentId, ?int $positionDepartmentId, bool $derive = true): ?int
    {
        if ($positionDepartmentId === null) {
            return $departmentId;
        }

        if ($departmentId === null) {
            return $derive ? $positionDepartmentId : null;
        }

        if ($departmentId !== $positionDepartmentId) {
            throw new \InvalidArgumentException(
                sprintf('O cargo selecionado pertence a outro departamento (ID %d).', $positionDepartmentId),
            );
        }

        return $departmentId;
    }
}
