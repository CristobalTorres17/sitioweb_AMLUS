<?php

namespace Database\Seeders\Legacy;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Article;

class LegacyArticlesSeeder extends Seeder
{
    /**
     * Convierte string desde latin1 a UTF-8 si es necesario
     */
    private function convertCharset($str)
    {
        if (empty($str)) {
            return $str;
        }
        if (!mb_check_encoding($str, 'UTF-8')) {
            return mb_convert_encoding($str, 'UTF-8', 'latin1');
        }
        return $str;
    }

    /**
     * Migra artículos/noticias desde BD legacy.
     *
     * Mapeo:
     * - noticias.id_noti → articles.id (nueva PK)
     * - noticias.id_catenoti → articles.category_id
     * - noticias.titulo → articles.title
     * - noticias.encabezado → articles.excerpt
     * - noticias.detalle → articles.content
     * - noticias.fecha → articles.published_at
     * - noticias.imagen → articles.image_path
     *
     * ⚠️ CHARSET: titulo/encabezado/detalle pueden tener mojibake (latin1)
     */
    public function run(): void
    {
        echo "\n=== MIGRANDO ARTÍCULOS ===\n";

        $legacyArticles = DB::connection('legacy')
            ->table('noticias')
            ->get();

        $count = 0;
        $created = 0;
        $failed = 0;

        foreach ($legacyArticles as $legacyArticle) {
            try {
                // Convertir ALL campos from latin1
                $title = $this->convertCharset(trim($legacyArticle->titulo));
                $excerpt = $this->convertCharset(trim($legacyArticle->encabezado));
                $content = $this->convertCharset(trim($legacyArticle->detalle));
                $imagePath = trim($legacyArticle->imagen) ?: null;

                // Parsear fecha (puede tener formato text)
                $publishedAt = null;
                try {
                    $publishedAt = Carbon::createFromFormat('Y-m-d', $legacyArticle->fecha)
                        ->startOfDay();
                } catch (\Exception $e) {
                    // Si no se puede parsear, usar hoy
                    $publishedAt = now();
                }

                $slug = Str::slug($title);

                // Crear artículo (sin author_id — no existe en schema)
                // category_id puede ser null si no existe en nueva BD
                $article = Article::updateOrCreate(
                    ['id' => $legacyArticle->id_noti],
                    [
                        'category_id' => null, // Legacy categories no existen, usar NULL
                        'title' => $title,
                        'slug' => $slug,
                        'excerpt' => $excerpt ?: null,
                        'content' => $content ?: null,
                        'image_path' => $imagePath,
                        'is_published' => true, // Todos los legacy son publicados
                        'published_at' => $publishedAt,
                    ]
                );

                echo "✓ Artículo ID {$legacyArticle->id_noti}: {$title}\n";
                $created++;
            } catch (\Exception $e) {
                echo "✗ Error migrando artículo {$legacyArticle->id_noti}: " . $e->getMessage() . "\n";
                $failed++;
            }

            $count++;
        }

        echo "\n📊 Resumen artículos:\n";
        echo "  Total procesados: {$count}\n";
        echo "  Creados/actualizados: {$created}\n";
        echo "  Errores: {$failed}\n";
    }
}
