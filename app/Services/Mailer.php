<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

class Mailer
{
    private array $config;

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? require BASE_PATH . '/config/mail.php';
    }

    public function send(string $to, string $subject, string $htmlBody, ?string $plainText = null): void
    {
        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Recipient email address is invalid.');
        }

        $fromEmail = (string) $this->config['from_email'];
        $fromName = (string) $this->config['from_name'];
        $host = (string) $this->config['host'];
        $port = (int) $this->config['port'];
        $timeout = (int) ($this->config['timeout'] ?? 10);

        $socket = @fsockopen($host, $port, $errorCode, $errorMessage, $timeout);

        if ($socket === false) {
            throw new RuntimeException("Unable to connect to SMTP server: {$errorMessage} ({$errorCode}).");
        }

        stream_set_timeout($socket, $timeout);

        try {
            $this->expect($socket, [220]);
            $this->command($socket, 'EHLO localhost', [250]);
            $this->command($socket, 'MAIL FROM:<' . $fromEmail . '>', [250]);
            $this->command($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
            $this->command($socket, 'DATA', [354]);
            $this->write($socket, $this->message($fromEmail, $fromName, $to, $subject, $htmlBody, $plainText));
            $this->write($socket, "\r\n.\r\n");
            $this->expect($socket, [250]);
            $this->command($socket, 'QUIT', [221]);
        } finally {
            fclose($socket);
        }
    }

    private function message(
        string $fromEmail,
        string $fromName,
        string $to,
        string $subject,
        string $htmlBody,
        ?string $plainText
    ): string {
        $boundary = 'onecv_' . bin2hex(random_bytes(12));
        $plainText ??= trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody)));

        $headers = [
            'From: ' . $this->formatAddress($fromEmail, $fromName),
            'To: <' . $to . '>',
            'Subject: ' . $this->encodeHeader($subject),
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];

        $body = [
            '--' . $boundary,
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            '',
            $this->dotStuff($plainText),
            '--' . $boundary,
            'Content-Type: text/html; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            '',
            $this->dotStuff($htmlBody),
            '--' . $boundary . '--',
        ];

        return implode("\r\n", $headers) . "\r\n\r\n" . implode("\r\n", $body);
    }

    private function command($socket, string $command, array $expectedCodes): void
    {
        $this->write($socket, $command . "\r\n");
        $this->expect($socket, $expectedCodes);
    }

    private function expect($socket, array $expectedCodes): void
    {
        $response = '';

        while (($line = fgets($socket, 512)) !== false) {
            $response .= $line;

            if (preg_match('/^\d{3}\s/', $line) === 1) {
                break;
            }
        }

        if ($response === '') {
            throw new RuntimeException('SMTP server did not respond.');
        }

        $code = (int) substr($response, 0, 3);

        if (! in_array($code, $expectedCodes, true)) {
            throw new RuntimeException('Unexpected SMTP response: ' . trim($response));
        }
    }

    private function write($socket, string $data): void
    {
        if (fwrite($socket, $data) === false) {
            throw new RuntimeException('Unable to write to SMTP socket.');
        }
    }

    private function formatAddress(string $email, string $name): string
    {
        return $this->encodeHeader($name) . ' <' . $email . '>';
    }

    private function encodeHeader(string $value): string
    {
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private function dotStuff(string $body): string
    {
        $body = str_replace(["\r\n", "\r"], "\n", $body);
        $lines = explode("\n", $body);

        foreach ($lines as &$line) {
            if (str_starts_with($line, '.')) {
                $line = '.' . $line;
            }
        }

        return implode("\r\n", $lines);
    }
}
