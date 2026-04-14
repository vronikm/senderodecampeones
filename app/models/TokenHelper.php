<?php

/**
 * TokenHelper — Generación de tokens HMAC para enlaces de inscripción.
 *
 * Se usa en el proyecto principal para generar los enlaces que se comparten
 * por WhatsApp. La validación se hace en el proyecto senderodecampeones_form.
 */

namespace app\models;

class TokenHelper
{
    /* Clave secreta — DEBE coincidir con la del proyecto senderodecampeones_form */
    const TOKEN_SECRET = 'sDc$2025!xK9mPqR7vLw3nBjF8hT1eZy';

    /* Tiempo de expiración por defecto: 72 horas */
    const TOKEN_EXPIRY = 259200;

    /* URL base del formulario */
    const FORM_URL = 'http://localhost/senderodecampeones_form/';

    /**
     * Genera un token firmado con HMAC-SHA256.
     */
    public static function generar(int $sedeId, int $expiraEnSegundos = self::TOKEN_EXPIRY): string
    {
        $payload = json_encode([
            'sede_id' => $sedeId,
            'exp'     => time() + $expiraEnSegundos
        ], JSON_UNESCAPED_UNICODE);

        $payloadB64   = self::base64urlEncode($payload);
        $signature    = hash_hmac('sha256', $payloadB64, self::TOKEN_SECRET);
        $signatureB64 = self::base64urlEncode($signature);

        return $payloadB64 . '.' . $signatureB64;
    }

    /**
     * Genera la URL completa del formulario con el token.
     */
    public static function generarURL(int $sedeId, int $expiraEnSegundos = self::TOKEN_EXPIRY): string
    {
        $token = self::generar($sedeId, $expiraEnSegundos);
        return self::FORM_URL . '?t=' . $token;
    }

    /**
     * Genera el enlace de WhatsApp con mensaje predeterminado.
     */
    public static function generarEnlaceWhatsApp(int $sedeId, string $sedeNombre, int $expiraEnSegundos = self::TOKEN_EXPIRY): string
    {
        $url  = self::generarURL($sedeId, $expiraEnSegundos);
        $mensaje = "Hola! Te comparto el enlace de inscripción para la escuela *Sendero de Campeones* - Sede *{$sedeNombre}*.\n\n"
                 . "Completa el formulario en el siguiente enlace:\n{$url}\n\n"
                 . "Este enlace tiene una vigencia de " . round($expiraEnSegundos / 3600) . " horas.";

        return 'https://wa.me/?text=' . rawurlencode($mensaje);
    }

    private static function base64urlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
