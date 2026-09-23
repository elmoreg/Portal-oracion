<?php

namespace App\Http\Controllers;

use App\Models\PrayerRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PrayerShareImageController extends Controller
{
    /**
     * Formatos disponibles: og (tarjeta para enlaces), square (feed de
     * Instagram) y story (Instagram/WhatsApp Stories).
     *
     * @var array<string, array{width:int, height:int, maxLines:int}>
     */
    private const FORMATS = [
        'og' => ['width' => 1200, 'height' => 630, 'maxLines' => 6],
        'square' => ['width' => 1080, 'height' => 1080, 'maxLines' => 9],
        'story' => ['width' => 1080, 'height' => 1920, 'maxLines' => 10],
    ];

    /**
     * Genera la imagen que se muestra al compartir una petición pública en
     * redes sociales. Solo las peticiones públicas son compartibles.
     */
    public function __invoke(Request $request, PrayerRequest $prayerRequest): Response
    {
        abort_unless($prayerRequest->is_public, 404);

        $format = $request->query('formato', 'og');
        $format = is_string($format) && isset(self::FORMATS[$format]) ? $format : 'og';

        $locale = app()->getLocale();

        $cacheKey = sprintf(
            'prayer-share-image:%s:%s:%s:%s',
            $prayerRequest->public_token,
            optional($prayerRequest->updated_at)->timestamp ?? 0,
            $locale,
            $format,
        );

        $png = Cache::remember($cacheKey, now()->addDay(), fn (): string => $this->render($prayerRequest, $format));

        $headers = [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ];

        if ($request->boolean('descargar')) {
            $headers['Content-Disposition'] = sprintf(
                'attachment; filename="peticion-%s-%s.png"',
                Str::slug($prayerRequest->requester_name ?: 'oracion'),
                $format,
            );
        }

        return response($png, 200, $headers);
    }

    private function render(PrayerRequest $prayerRequest, string $format): string
    {
        ['width' => $width, 'height' => $height, 'maxLines' => $maxLines] = self::FORMATS[$format];

        $image = imagecreatetruecolor($width, $height);

        $background = imagecolorallocate($image, 2, 6, 23);      // slate-950
        $amber = imagecolorallocate($image, 245, 158, 11);       // amber-500
        $white = imagecolorallocate($image, 248, 250, 252);      // slate-50
        $muted = imagecolorallocate($image, 148, 163, 184);      // slate-400

        imagefill($image, 0, 0, $background);
        imagealphablending($image, true);

        $this->paintAmberGlow($image, $width);

        // Barra de acento a la izquierda.
        $barWidth = (int) round($width * 0.012);
        imagefilledrectangle($image, 0, 0, $barWidth, $height, $amber);

        $serif = resource_path('fonts/Lora-Bold.ttf');
        $sans = resource_path('fonts/Inter-Regular.ttf');

        $marginX = (int) round($width * 0.075);
        $contentWidth = $width - ($marginX * 2);

        // Cabecera de marca.
        $headerY = (int) round($height * 0.11);
        $iconR = (int) round($width * 0.02);
        imagefilledellipse($image, $marginX + $iconR, $headerY - $iconR, $iconR * 2, $iconR * 2, $amber);
        imagettftext($image, $iconR, 0, $marginX + (int) ($iconR * 0.55), $headerY - (int) ($iconR * 0.35), $background, $sans, '+');
        imagettftext($image, (int) round($width * 0.017), 0, $marginX + ($iconR * 3), $headerY - (int) ($iconR * 0.15), $white, $sans, mb_strtoupper(__('Portal de Oración')));

        // Cita de la petición.
        $excerpt = $this->excerpt($prayerRequest->translated_content ?? $prayerRequest->content ?? '');
        $quote = '“'.$excerpt.'”';

        $fontSize = (int) round($width * 0.037);
        $lineHeight = (int) round($fontSize * 1.5);
        $lines = $this->wrapText($quote, $fontSize, $serif, $contentWidth);

        // Reduce el tamaño si el texto es muy largo para el formato.
        while (count($lines) > $maxLines && $fontSize > (int) round($width * 0.024)) {
            $fontSize -= 3;
            $lineHeight = (int) round($fontSize * 1.5);
            $lines = $this->wrapText($quote, $fontSize, $serif, $contentWidth);
        }

        $lines = array_slice($lines, 0, $maxLines);

        $blockHeight = count($lines) * $lineHeight;
        $startY = (int) (($height - $blockHeight) / 2) + $fontSize;

        foreach ($lines as $index => $line) {
            imagettftext($image, $fontSize, 0, $marginX, $startY + ($index * $lineHeight), $white, $serif, $line);
        }

        // Atribución.
        $name = trim((string) $prayerRequest->requester_name);
        $attribution = $name !== ''
            ? __(':name pide tu oración', ['name' => $name])
            : __('Alguien pide tu oración');

        $attributionSize = (int) round($width * 0.018);
        imagettftext($image, $attributionSize, 0, $marginX, $height - (int) round($height * 0.11), $amber, $sans, mb_strtoupper($attribution));

        // Pie.
        $footerSize = (int) round($width * 0.014);
        imagettftext($image, $footerSize, 0, $marginX, $height - (int) round($height * 0.06), $muted, $sans, mb_strtoupper(__('Únete a orar').' · '.parse_url((string) config('app.url'), PHP_URL_HOST)));

        ob_start();
        imagepng($image);
        $png = (string) ob_get_clean();
        imagedestroy($image);

        return $png;
    }

    private function paintAmberGlow(\GdImage $image, int $width): void
    {
        // Un resplandor ámbar en la esquina superior derecha, dibujado como
        // círculos concéntricos muy translúcidos para simular un degradado.
        $cx = $width - 40;
        $cy = -20;
        $maxRadius = (int) round($width * 0.55);

        for ($radius = $maxRadius; $radius > 0; $radius -= 6) {
            $alpha = (int) round(126 - (($maxRadius - $radius) / $maxRadius * 18));
            $alpha = min(126, max(108, $alpha));
            $color = imagecolorallocatealpha($image, 245, 158, 11, $alpha);
            imagefilledellipse($image, $cx, $cy, $radius, $radius, $color);
        }
    }

    private function excerpt(string $text): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text));

        return Str::limit($text, 220, '…');
    }

    /**
     * @return list<string>
     */
    private function wrapText(string $text, int $size, string $font, int $maxWidth): array
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $trial = $current === '' ? $word : $current.' '.$word;
            $bbox = imagettfbbox($size, 0, $font, $trial);
            $lineWidth = $bbox[2] - $bbox[0];

            if ($lineWidth > $maxWidth && $current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $trial;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }
}
