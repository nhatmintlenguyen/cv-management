<?php

$config = require __DIR__ . '/../config/database.php';
$schemaPath = __DIR__ . '/../schema.sql';

if (! file_exists($schemaPath)) {
    exit("Cannot find schema.sql\n");
}

$dsn = sprintf(
    'mysql:host=%s;port=%d;charset=%s',
    $config['host'],
    $config['port'],
    $config['charset']
);

try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $database = str_replace('`', '``', $config['database']);
    $collation = str_replace('`', '``', $config['collation']);
    $charset = str_replace('`', '``', $config['charset']);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET {$charset} COLLATE {$collation}");
    $pdo->exec("USE `{$database}`");

    foreach (parseSqlFile($schemaPath) as $statement) {
        if (trim($statement) !== '') {
            $pdo->exec($statement);
        }
    }

    echo "Database '{$config['database']}' was created and schema.sql was imported successfully.\n";
} catch (PDOException $exception) {
    exit("Database setup failed: {$exception->getMessage()}\n");
}

function parseSqlFile(string $path): array
{
    $statements = [];
    $delimiter = ';';
    $buffer = '';
    $lines = file($path, FILE_IGNORE_NEW_LINES);

    foreach ($lines as $line) {
        $trimmedLine = trim($line);

        if ($trimmedLine === '' || str_starts_with($trimmedLine, '--')) {
            continue;
        }

        if (str_starts_with(strtoupper($trimmedLine), 'DELIMITER ')) {
            $delimiter = trim(substr($trimmedLine, strlen('DELIMITER ')));
            continue;
        }

        $buffer .= $line . "\n";
        $trimmedBuffer = rtrim($buffer);

        if (str_ends_with($trimmedBuffer, $delimiter)) {
            $statement = substr($trimmedBuffer, 0, -strlen($delimiter));
            $statements[] = trim($statement);
            $buffer = '';
        }
    }

    if (trim($buffer) !== '') {
        $statements[] = trim($buffer);
    }

    return $statements;
}
