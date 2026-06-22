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
        'options_order',
        'is_doubtful',
        'is_correct',
        'points',
    ];

    protected $casts = [
        'selected_answer' => 'string',
        'options_order' => 'json',
        'is_doubtful' => 'boolean',
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

