<?php
/**
 * La Bonne Occaz — Mailer SMTP
 *
 * Utilise SMTP authentifié Hostinger (smtp.hostinger.com:465 SSL)
 * pour garantir la délivrabilité et éviter les spams.
 *
 * Configuration : Admin → Paramètres → Email (ou variables d'env)
 * - SMTP_HOST     : smtp.hostinger.com
 * - SMTP_PORT     : 465
 * - SMTP_USER     : votre email Hostinger (ex: contact@labonneoccaz.fr)
 * - SMTP_PASS     : mot de passe de votre boîte email Hostinger
 */
class Mailer {

    // ── Config SMTP ─────────────────────────────────────────────────
    // Modifiez ces valeurs avec vos identifiants Hostinger
    private static string $smtpHost = 'smtp.hostinger.com';
    private static int    $smtpPort = 465; // SSL
    private static string $smtpUser = ''; // rempli dynamiquement depuis cfg_email()
    private static string $smtpPass = ''; // à définir ci-dessous

    /**
     * Mot de passe SMTP — à renseigner ici ou via variable d'environnement.
     * Ne mettez PAS ce fichier dans Git si vous y mettez le mot de passe en clair.
     * Préférez : define('SMTP_PASSWORD', 'votre_mdp') dans config/database.php
     */
    private static function getSmtpPass(): string {
        // Priorité 1 : constante définie dans database.php
        if (defined('SMTP_PASSWORD')) return SMTP_PASSWORD;
        // Priorité 2 : variable d'environnement serveur
        $env = getenv('SMTP_PASSWORD');
        if ($env !== false) return $env;
        // Priorité 3 : valeur statique (à éviter en production)
        return self::$smtpPass;
    }

    // ── Envoi SMTP ──────────────────────────────────────────────────

    /**
     * Envoie un email via SMTP authentifié Hostinger.
     */
    public static function send(string $to, string $subject, string $html, string $replyTo = ''): bool {
        $from    = cfg_email();
        $pass    = self::getSmtpPass();

        // Si pas de mot de passe SMTP configuré → fallback mail() avec headers améliorés
        if (empty($pass)) {
            return self::sendFallback($to, $subject, $html, $from, $replyTo);
        }

        try {
            return self::sendSmtp($to, $subject, $html, $from, $pass, $replyTo);
        } catch (\Exception $e) {
            // Log l'erreur silencieusement et tente le fallback
            error_log('[Mailer] SMTP error: ' . $e->getMessage());
            return self::sendFallback($to, $subject, $html, $from, $replyTo);
        }
    }

    /**
     * Envoi SMTP SSL natif (sans PHPMailer).
     */
    private static function sendSmtp(
        string $to, string $subject, string $html,
        string $from, string $pass, string $replyTo
    ): bool {
        $host    = self::$smtpHost;
        $port    = self::$smtpPort;
        $timeout = 15;

        // Connexion SSL
        $ctx = stream_context_create(['ssl' => [
            'verify_peer'       => true,
            'verify_peer_name'  => true,
            'allow_self_signed' => false,
        ]]);

        $socket = @stream_socket_client(
            "ssl://{$host}:{$port}",
            $errno, $errstr, $timeout,
            STREAM_CLIENT_CONNECT,
            $ctx
        );

        if (!$socket) {
            throw new \Exception("Connexion SMTP impossible : $errstr ($errno)");
        }

        stream_set_timeout($socket, $timeout);

        $read = function() use ($socket) {
            $resp = '';
            while ($line = fgets($socket, 512)) {
                $resp .= $line;
                if ($line[3] === ' ') break; // fin de réponse multi-ligne
            }
            return $resp;
        };

        $write = function(string $cmd) use ($socket) {
            fwrite($socket, $cmd . "\r\n");
        };

        $expect = function(string $code) use ($read) {
            $resp = $read();
            if (substr($resp, 0, 3) !== $code) {
                throw new \Exception("SMTP: attendu $code, reçu: $resp");
            }
            return $resp;
        };

        // Dialogue SMTP
        $read(); // Bannière
        $write("EHLO " . (parse_url(BASE_URL, PHP_URL_HOST) ?: 'localhost'));
        $expect('250');

        // Auth LOGIN
        $write("AUTH LOGIN");
        $expect('334');
        $write(base64_encode($from));
        $expect('334');
        $write(base64_encode($pass));
        $expect('235');

        // Envoi
        $write("MAIL FROM:<{$from}>");
        $expect('250');

        // Destinataires multiples supportés
        foreach (array_map('trim', explode(',', $to)) as $recipient) {
            $write("RCPT TO:<{$recipient}>");
            $expect('250');
        }

        $write("DATA");
        $expect('354');

        // Construction du message RFC 2822
        $msgId   = '<' . uniqid('lbo', true) . '@' . (parse_url(BASE_URL, PHP_URL_HOST) ?: 'labonneoccaz.fr') . '>';
        $headers = [
            "From: La Bonne Occaz <{$from}>",
            "To: {$to}",
            "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
            "Date: " . date('r'),
            "Message-ID: {$msgId}",
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8",
            "Content-Transfer-Encoding: base64",
            "X-Mailer: LaBonneOccaz/2.0",
        ];
        if ($replyTo && $replyTo !== $from) {
            $headers[] = "Reply-To: {$replyTo}";
        }

        $body = chunk_split(base64_encode($html));
        $message = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.\r\n";
        $write($message);
        $expect('250');

        $write("QUIT");
        fclose($socket);

        return true;
    }

    /**
     * Fallback mail() si SMTP non configuré.
     */
    private static function sendFallback(string $to, string $subject, string $html, string $from, string $replyTo): bool {
        $msgId   = '<' . uniqid('lbo', true) . '@' . (parse_url(BASE_URL, PHP_URL_HOST) ?: 'labonneoccaz.fr') . '>';
        $headers = implode("\r\n", array_filter([
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8",
            "Content-Transfer-Encoding: base64",
            "From: La Bonne Occaz <{$from}>",
            $replyTo && $replyTo !== $from ? "Reply-To: {$replyTo}" : '',
            "Message-ID: {$msgId}",
            "Date: " . date('r'),
            "X-Mailer: LaBonneOccaz/2.0",
        ]));
        return mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', chunk_split(base64_encode($html)), $headers);
    }

    // ── Wrapper HTML ─────────────────────────────────────────────────

    public static function wrap(string $title, string $content): string {
        return '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">
<style>
  body{font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:0}
  .wrap{max-width:600px;margin:30px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1)}
  .head{background:#e63946;padding:24px 32px;color:#fff}
  .head h1{margin:0;font-size:1.3rem;font-weight:700}
  .head p{margin:4px 0 0;font-size:.85rem;opacity:.85}
  .body{padding:28px 32px;color:#333;font-size:.92rem;line-height:1.7}
  .row{display:flex;gap:8px;margin:6px 0}
  .label{font-weight:700;min-width:130px;color:#555}
  .val{color:#1d1d1b}
  .section{background:#f8f9fa;border-radius:6px;padding:14px 18px;margin:16px 0;border-left:4px solid #e63946}
  .section h3{margin:0 0 10px;font-size:1rem;color:#e63946}
  .btn{display:inline-block;background:#e63946;color:#fff;padding:10px 24px;border-radius:24px;text-decoration:none;font-weight:700;margin-top:16px}
  .foot{background:#1d1d1b;padding:16px 32px;color:rgba(255,255,255,.5);font-size:.78rem;text-align:center}
</style></head><body>
<div class="wrap">
  <div class="head"><h1>La Bonne Occaz</h1><p>' . htmlspecialchars($title) . '</p></div>
  <div class="body">' . $content . '</div>
  <div class="foot">La Bonne Occaz &mdash; ' . htmlspecialchars(cfg_address()) . '<br>' . htmlspecialchars(cfg_phone()) . ' &mdash; ' . htmlspecialchars(cfg_email()) . '</div>
</div></body></html>';
    }

    // ── Templates ────────────────────────────────────────────────────

    public static function reservationAdmin(array $v, int $id, array $post): string {
        $fn    = htmlspecialchars($post['first_name'] ?? '');
        $ln    = htmlspecialchars($post['last_name']  ?? '');
        $em    = htmlspecialchars($post['email']      ?? '');
        $ph    = htmlspecialchars($post['phone']      ?? '');
        $city  = htmlspecialchars($post['city']       ?? '—');
        $msg   = nl2br(htmlspecialchars($post['message'] ?? ''));
        $brand = htmlspecialchars(($v['brand_name'] ?? '') . ' ' . $v['model']);
        $year  = htmlspecialchars($v['year'] ?? '');
        $price = number_format((float)$v['price'], 0, ',', ' ') . ' €';
        $url   = BASE_URL . '/admin/reservations/view/' . $id;

        $content = "
        <p>Nouvelle réservation <strong>#$id</strong> enregistrée.</p>
        <div class='section'>
          <h3>Client</h3>
          <div class='row'><span class='label'>Nom :</span><span class='val'>$fn $ln</span></div>
          <div class='row'><span class='label'>Email :</span><span class='val'><a href='mailto:$em'>$em</a></span></div>
          <div class='row'><span class='label'>Téléphone :</span><span class='val'>$ph</span></div>
          <div class='row'><span class='label'>Ville :</span><span class='val'>$city</span></div>
        </div>
        <div class='section'>
          <h3>Véhicule</h3>
          <div class='row'><span class='label'>Véhicule :</span><span class='val'>$brand ($year)</span></div>
          <div class='row'><span class='label'>Prix :</span><span class='val'>$price</span></div>
        </div>
        " . ($msg ? "<div class='section'><h3>Message</h3><p>$msg</p></div>" : '') . "
        <a href='$url' class='btn'>Voir dans l'administration</a>";

        return self::wrap("Réservation #$id — $brand", $content);
    }

    public static function reservationClient(array $v, array $post): string {
        $fn    = htmlspecialchars($post['first_name'] ?? '');
        $brand = htmlspecialchars(($v['brand_name'] ?? '') . ' ' . $v['model']);
        $year  = htmlspecialchars($v['year'] ?? '');
        $price = number_format((float)$v['price'], 0, ',', ' ') . ' €';
        $phone = htmlspecialchars(cfg_phone());

        $content = "
        <p>Bonjour <strong>$fn</strong>,</p>
        <p>Votre réservation a bien été enregistrée. Notre équipe vous contactera dans les <strong>24 heures</strong>.</p>
        <div class='section'>
          <h3>Récapitulatif</h3>
          <div class='row'><span class='label'>Véhicule :</span><span class='val'>$brand ($year)</span></div>
          <div class='row'><span class='label'>Prix :</span><span class='val'>$price</span></div>
        </div>
        <p>Pour toute question : <strong>$phone</strong></p>
        <p>Merci de votre confiance,<br><strong>L'équipe La Bonne Occaz</strong></p>";

        return self::wrap("Confirmation de réservation", $content);
    }

    public static function sellingAdmin(array $d, int $id): string {
        $fn    = htmlspecialchars($d['first_name']);
        $ln    = htmlspecialchars($d['last_name']);
        $em    = htmlspecialchars($d['email']);
        $ph    = htmlspecialchars($d['phone']);
        $city  = htmlspecialchars($d['city'] ?? '—');
        $brand = htmlspecialchars($d['brand'] . ' ' . $d['model']);
        $year  = htmlspecialchars($d['year']);
        $km    = number_format((int)$d['mileage'], 0, ',', ' ') . ' km';
        $fuel  = htmlspecialchars($d['fuel_type']);
        $price = $d['desired_price'] ? number_format($d['desired_price'], 0, ',', ' ') . ' €' : 'Non renseigné';
        $desc  = nl2br(htmlspecialchars($d['description'] ?? ''));
        $url   = BASE_URL . '/admin/demandes';

        $content = "
        <p>Nouvelle demande de dépôt-vente <strong>#$id</strong>.</p>
        <div class='section'>
          <h3>Client</h3>
          <div class='row'><span class='label'>Nom :</span><span class='val'>$fn $ln</span></div>
          <div class='row'><span class='label'>Email :</span><span class='val'><a href='mailto:$em'>$em</a></span></div>
          <div class='row'><span class='label'>Téléphone :</span><span class='val'>$ph</span></div>
          <div class='row'><span class='label'>Ville :</span><span class='val'>$city</span></div>
        </div>
        <div class='section'>
          <h3>Véhicule proposé</h3>
          <div class='row'><span class='label'>Véhicule :</span><span class='val'>$brand ($year)</span></div>
          <div class='row'><span class='label'>Kilométrage :</span><span class='val'>$km</span></div>
          <div class='row'><span class='label'>Carburant :</span><span class='val'>$fuel</span></div>
          <div class='row'><span class='label'>Prix souhaité :</span><span class='val'>$price</span></div>
        </div>
        " . ($desc ? "<div class='section'><h3>Description</h3><p>$desc</p></div>" : '') . "
        <a href='$url' class='btn'>Voir dans l'administration</a>";

        return self::wrap("Demande de vente #$id — $brand", $content);
    }

    public static function contactAdmin(string $fn, string $ln, string $email, string $phone, string $city, string $msg, int $id): string {
        $fn   = htmlspecialchars($fn);
        $ln   = htmlspecialchars($ln);
        $em   = htmlspecialchars($email);
        $ph   = htmlspecialchars($phone);
        $ci   = htmlspecialchars($city);
        $body = nl2br(htmlspecialchars($msg));
        $url  = BASE_URL . '/admin/demandes';

        $content = "
        <p>Nouveau message de contact <strong>#$id</strong>.</p>
        <div class='section'>
          <h3>Expéditeur</h3>
          <div class='row'><span class='label'>Nom :</span><span class='val'>$fn $ln</span></div>
          <div class='row'><span class='label'>Email :</span><span class='val'><a href='mailto:$em'>$em</a></span></div>
          <div class='row'><span class='label'>Téléphone :</span><span class='val'>" . ($ph ?: '—') . "</span></div>
          <div class='row'><span class='label'>Ville :</span><span class='val'>$ci</span></div>
        </div>
        <div class='section'><h3>Message</h3><p>$body</p></div>
        <a href='$url' class='btn'>Voir dans l'administration</a>";

        return self::wrap("Message contact #$id — $fn $ln", $content);
    }

    public static function vehicleContactAdmin(string $fn, string $ln, string $email, string $phone, string $msg, int $id, string $vehicleLabel, string $typeLabel): string {
        $fn    = htmlspecialchars($fn);
        $ln    = htmlspecialchars($ln);
        $em    = htmlspecialchars($email);
        $ph    = htmlspecialchars($phone);
        $body  = nl2br(htmlspecialchars($msg));
        $veh   = htmlspecialchars($vehicleLabel);
        $type  = htmlspecialchars($typeLabel);
        $url   = BASE_URL . '/admin/demandes';

        $content = "
        <p>Nouvelle <strong>$type</strong> <strong>#$id</strong> depuis une fiche véhicule.</p>
        <div class='section'>
          <h3>Client</h3>
          <div class='row'><span class='label'>Nom :</span><span class='val'>$fn $ln</span></div>
          <div class='row'><span class='label'>Email :</span><span class='val'><a href='mailto:$em'>$em</a></span></div>
          <div class='row'><span class='label'>Téléphone :</span><span class='val'>" . ($ph ?: '—') . "</span></div>
        </div>
        " . ($veh ? "<div class='section'><h3>Véhicule concerné</h3><p><strong>$veh</strong></p></div>" : '') . "
        <div class='section'><h3>Message</h3><p>$body</p></div>
        <a href='$url' class='btn'>Voir dans l'administration</a>";

        return self::wrap("$typeLabel #$id" . ($veh ? " — $veh" : ''), $content);
    }
}
