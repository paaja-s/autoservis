<?php
return [
	'paths' => ['api/*'], // Povolené cesty
	'allowed_methods' => ['*'],                 // Povolené HTTP metody (GET, POST, PUT, DELETE atd.)
	'allowed_origins' => ['*'], // Povolené originy
	'allowed_origins_patterns' => [],           // Regulární výrazy pro originy, pokud potřebujete více flexibilní pravidla
	'allowed_headers' => ['*'],                 // Povolené hlavičky
	'exposed_headers' => [],                    // Hlavičky, které mohou být dostupné na straně klienta
	'max_age' => 0,                             // Maximální doba cacheování preflight odpovědi (v sekundách)
	'supports_credentials' => true,             // Povolení přenosu cookies nebo tokenů
];