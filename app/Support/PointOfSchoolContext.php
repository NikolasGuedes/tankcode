<?php

namespace App\Support;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PointOfSchoolContext
{
    public const SESSION_KEY = 'current_point_of_school_id';

    public static function supports(?User $user): bool
    {
        return $user?->hasRole(RoleEnum::OWNER, RoleEnum::DIRECTOR, RoleEnum::TEACHER) ?? false;
    }

    /**
     * @return Collection<int, array{id: int, name: string, cnpj: string|null}>
     */
    public static function assignedPoints(?User $user): Collection
    {
        if (! self::supports($user)) {
            return collect();
        }

        return $user->pointOfSchools()
            ->orderBy('point_of_schools.name')
            ->get(['point_of_schools.id', 'point_of_schools.name', 'point_of_schools.cnpj'])
            ->map(fn ($point) => [
                'id' => (int) $point->id,
                'name' => $point->name,
                'cnpj' => $point->cnpj,
            ])
            ->values();
    }

    /**
     * @param  Collection<int, array{id: int, name: string, cnpj: string|null}>|null  $assignedPoints
     * @return array{id: 'all'|int, name: string, cnpj: string|null}|null
     */
    public static function current(Request $request, ?User $user, ?Collection $assignedPoints = null): ?array
    {
        if (! self::supports($user)) {
            return null;
        }

        $assignedPoints ??= self::assignedPoints($user);

        if ($assignedPoints->isEmpty()) {
            return null;
        }

        $currentPointId = $request->session()->get(self::SESSION_KEY);

        if ($currentPointId === null) {
            $request->session()->put(self::SESSION_KEY, 'all');

            return [
                'id' => 'all',
                'name' => 'Todos os Pontos',
                'cnpj' => null,
            ];
        }

        if ($currentPointId === 'all') {
            return [
                'id' => 'all',
                'name' => 'Todos os Pontos',
                'cnpj' => null,
            ];
        }

        $currentPoint = $assignedPoints->firstWhere('id', (int) $currentPointId);

        if ($currentPoint) {
            return $currentPoint;
        }

        $request->session()->put(self::SESSION_KEY, 'all');

        return [
            'id' => 'all',
            'name' => 'Todos os Pontos',
            'cnpj' => null,
        ];
    }

    /**
     * @param  Collection<int, array{id: int, name: string, cnpj: string|null}>|null  $assignedPoints
     * @return Collection<int, int>
     */
    public static function selectedPointIds(Request $request, ?User $user, ?Collection $assignedPoints = null): Collection
    {
        if (! self::supports($user)) {
            return collect();
        }

        $assignedPoints ??= self::assignedPoints($user);
        $currentPoint = self::current($request, $user, $assignedPoints);

        if (! $currentPoint) {
            return collect();
        }

        if ($currentPoint['id'] === 'all') {
            return $assignedPoints->pluck('id')->map(fn ($id) => (int) $id)->values();
        }

        return collect([(int) $currentPoint['id']]);
    }

    /**
     * @param  string  $selection
     */
    public static function set(Request $request, ?User $user, string $selection): bool
    {
        if (! self::supports($user)) {
            return false;
        }

        if ($selection === 'all') {
            $request->session()->put(self::SESSION_KEY, 'all');

            return true;
        }

        $assignedPoints = self::assignedPoints($user);
        $point = $assignedPoints->firstWhere('id', (int) $selection);

        if (! $point) {
            return false;
        }

        $request->session()->put(self::SESSION_KEY, (int) $point['id']);

        return true;
    }
}
