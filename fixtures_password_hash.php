<?php

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

require_once __DIR__ . '/vendor/autoload.php';

// Créer un hasher
$hasher = new NativePasswordHasher();

// Mot de passe à hacher
$plainPassword = 'coucou';

// Hacher le mot de passe
$hashedPassword = $hasher->hash($plainPassword);

// Afficher le résultat
echo "Mot de passe : $plainPassword\n";
echo "Haché : $hashedPassword\n";
