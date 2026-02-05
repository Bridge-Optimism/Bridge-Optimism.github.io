<?php
// FAQ Schema (JSON-LD) — auto-generated
$faq_items = [
    ['q' => 'How do I bridge assets to Optimism using this bridge?', 'a' => 'Open the dashboard via the central button, connect your wallet, select the token and amount, and confirm the deposit transaction. The interface shows required steps, estimated fees, and transaction status until completion.'],
    ['q' => 'What fees should I expect when using the Optimism Bridge?', 'a' => 'Fees typically include the source-chain gas and any bridge-specific fees; using Optimism’s rollups generally lowers overall gas costs. The dashboard displays estimated fees before you confirm so you can review costs in advance.'],
    ['q' => 'Which tokens and networks are supported?', 'a' => 'Supported tokens and source networks may change as the project evolves; check the bridge dashboard or the project GitHub for the current list. Always verify token contract addresses and supported pairs before initiating a transfer.'],
    ['q' => 'Is bridging through Bridge-Optimism safe?', 'a' => 'Bridge-Optimism is open-source and community-reviewed, and its contracts follow established security patterns; however, no system is risk-free. For extra safety, verify addresses, review transaction details, and consider a small test transfer first.']
];

$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => []
];

foreach ($faq_items as $item) {
    $schema['mainEntity'][] = [
        '@type' => 'Question',
        'name' => $item['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $item['a'],
        ],
    ];
}

echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
?>