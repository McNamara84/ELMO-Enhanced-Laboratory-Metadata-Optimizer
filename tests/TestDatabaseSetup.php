<?php
namespace Tests;

function setupTestDatabase($connection)
{
    try {
        // Bestehende Tabellen löschen
        require_once __DIR__ . '/../install.php';

        dropTables($connection);

        // Datenbankstruktur erstellen
        $result = createDatabaseStructure($connection);
        if ($result['status'] === 'error') {
            throw new \Exception($result['message']);
        }

        // Minimale Lookup-Daten für Tests einfügen
        insertLookupData($connection);

        return true;

    } catch (\Exception $e) {
        throw new \Exception("Fehler beim Aufsetzen der Testdatenbank: " . $e->getMessage());
    }
}