<?php
$cfg = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
if (!$cfg || !isset($cfg['brand'])) { die('Invalid config.json'); }

$brand   = htmlspecialchars($cfg['brand'] ?? '');
$keyword = htmlspecialchars($cfg['keyword'] ?? '');
$seo     = $cfg['seo'] ?? [];
$titleTpl = $seo['title_template'] ?? ($keyword . ' \xe2\x80\x94 Official Site');
$metaDesc = $seo['meta_description'] ?? '';
$pageTitle = str_replace(['{keyword}', '{brand}'], [$keyword, $brand], $titleTpl);
$pageDesc  = str_replace(['{keyword}', '{brand}'], [$keyword, $brand], $metaDesc);
$canonical = 'https://' . ($_SERVER['HTTP_HOST'] ?? '') . strtok($_SERVER['REQUEST_URI'] ?? '/', '?');

// Build Sniper <head> injection
$sniperHead  = '    <title>' . htmlspecialchars($pageTitle) . '</title>' . "\n";
$sniperHead .= '    <meta name="description" content="' . htmlspecialchars($pageDesc) . '">' . "\n";
$sniperHead .= '    <meta name="robots" content="index, follow">' . "\n";
$sniperHead .= '    <link rel="canonical" href="' . htmlspecialchars($canonical) . '">' . "\n";
$sniperHead .= '    <meta property="og:title" content="' . htmlspecialchars($pageTitle) . '">' . "\n";
$sniperHead .= '    <meta property="og:description" content="' . htmlspecialchars($pageDesc) . '">' . "\n";
$sniperHead .= '    <meta property="og:type" content="website">' . "\n";
$sniperHead .= '    <link rel="stylesheet" href="assets/style.css">' . "\n";

// Capture Sniper parts via output buffering
ob_start(); include __DIR__ . '/sniper_top.php'; $sniperTop = ob_get_clean();
ob_start(); include __DIR__ . '/sniper_bottom.php'; $sniperBottom = ob_get_clean();
ob_start(); include __DIR__ . '/sniper_schema.php'; $sniperSchema = ob_get_clean();

// Capture site.php output
ob_start();
if (file_exists(__DIR__ . '/site.php')) { include __DIR__ . '/site.php'; }
$siteHtml = ob_get_clean();

$delayedJs = '';

// --- Injection logic ---
$hasHead = (stripos($siteHtml, '</head>') !== false);
$hasBody = (stripos($siteHtml, '<body') !== false);

if ($hasHead && $hasBody) {
    // INJECT MODE: site has full HTML structure
    $siteHtml = str_ireplace('</head>', $sniperHead . $sniperSchema . '</head>', $siteHtml);

    $bodyPos = stripos($siteHtml, '<body');
    if ($bodyPos !== false) {
        $closePos = strpos($siteHtml, '>', $bodyPos);
        if ($closePos !== false) {
            $siteHtml = substr($siteHtml, 0, $closePos + 1)
                       . "\n<div class=\"snpr-wrap\">\n" . $sniperTop . "</div>\n"
                      . substr($siteHtml, $closePos + 1);
        }
    }

    $siteHtml = str_ireplace('</body>', "\n<div class=\"snpr-wrap\">\n" . $sniperBottom . "</div>\n" . $delayedJs . '</body>', $siteHtml);
    echo $siteHtml;
} else {
    // FALLBACK MODE: site outputs plain content, build full HTML
    echo '<!DOCTYPE html>' . "\n" . '<html lang="en">' . "\n" . '<head>' . "\n";
    echo '    <meta charset="UTF-8">' . "\n" . '    <meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
    echo $sniperHead . $sniperSchema;
    echo '</head>' . "\n" . '<body>' . "\n";
    echo '<div class="snpr-wrap">' . "\n" . $sniperTop . '</div>' . "\n";
    echo $siteHtml;
    echo "\n" . '<div class="snpr-wrap">' . "\n" . $sniperBottom . '</div>' . "\n";
    echo $delayedJs;
    echo '</body>' . "\n" . '</html>';
}
