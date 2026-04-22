<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UjianAnswer extends Model
{
    use HasFactory;

    protected $table = 'ujian_answers';

    protected $fillable = [
        'ujian_result_id',
        'question_id',
        'selected_answer',
        'is_correct',
        'points',
    ];

    protected $casts = [
'selected_answer' => 'string',
        'is_correct' => 'boolean',
        'points' => 'decimal:2',
    ];

    public function ujianResult(): BelongsTo
    {
        return $this->belongsTo(UjianResult::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}

