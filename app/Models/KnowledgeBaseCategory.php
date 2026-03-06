<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseCategory extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'order'];

    public function pages()
    {
        return $this->hasMany(KnowledgeBasePage::class, 'category_id')->orderBy('order');
    }
}
