<?php

declare(strict_types=1);

namespace Domain\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $version
 * @property string $system_prompt
 * @property string $user_template
 * @property string $model
 * @property float $temperature
 * @property string $status
 */
class PromptTemplate extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'version',
        'system_prompt',
        'user_template',
        'model',
        'temperature',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'temperature' => 'float',
        ];
    }
}
