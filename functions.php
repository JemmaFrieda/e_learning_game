<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function redirect(string $path): never
{
    header('Location: ' . app_url($path));
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function post_string(string $key): string
{
    return trim((string)($_POST[$key] ?? ''));
}

function youtube_embed_url(?string $url): ?string
{
    $url = trim((string)$url);
    if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) return null;
    $parts = parse_url($url);
    $host = strtolower((string)($parts['host'] ?? ''));
    $videoId = null;
    if (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com'], true)) {
        parse_str((string)($parts['query'] ?? ''), $query);
        $videoId = $query['v'] ?? null;
        if (!$videoId && isset($parts['path']) && str_starts_with($parts['path'], '/embed/')) $videoId = substr($parts['path'], 7);
    } elseif ($host === 'youtu.be') {
        $videoId = trim((string)($parts['path'] ?? ''), '/');
    }
    return is_string($videoId) && preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId) ? 'https://www.youtube.com/embed/' . $videoId : null;
}
