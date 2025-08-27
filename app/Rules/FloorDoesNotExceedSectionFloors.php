<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Section;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FloorDoesNotExceedSectionFloors implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $sectionId = request()->input('section_id');

        if (! $sectionId) {
            return;
        }

        $section = Section::query()->find($sectionId);

        if (! $section) {
            $fail('Cекция не найдена или не выбрана.');

            return;
        }

        if ($value > $section->floors_count) {
            $fail("Этаж не может быть больше {$section->floors_count} (макс. этаж в секции).");
        }
    }
}
