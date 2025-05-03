<?php
namespace Tests;
use PHPUnit\Framework\TestCase;
use mysqli_sql_exception;

require_once __DIR__ . '/../settings.php';
require_once __DIR__ . '/../save/formgroups/save_originatinglaboratory.php';
require_once __DIR__ . '/TestDatabaseSetup.php';

class SaveOriginatingLaboratoryTest extends TestCase
{
    private $connection;

    protected function setUp(): void
    {
        global $connection;
        if (!$connection) {
            $connection = connectDb();
        }
        $this->connection = $connection;

        // Testdatenbank auswählen oder anlegen
        $dbname = 'mde2-msl-test';
        try {
            if ($this->connection->select_db($dbname) === false) {
                // Testdatenbank erstellen
                $connection->query("CREATE DATABASE " . $dbname);
                $connection->select_db($dbname);
            }

            // Datenbank für Tests aufsetzen
            setupTestDatabase($connection);

        } catch (\Exception $e) {
            $this->fail("Fehler beim Setup der Testdatenbank: " . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        $this->cleanupTestData();
    }

    private function cleanupTestData()
    {
        $this->connection->query("SET FOREIGN_KEY_CHECKS=0");
        $this->connection->query("DELETE FROM Resource_has_Spatial_Temporal_Coverage");
        $this->connection->query("DELETE FROM Resource_has_Thesaurus_Keywords");
        $this->connection->query("DELETE FROM Resource_has_Related_Work");
        $this->connection->query("DELETE FROM Resource_has_Originating_Laboratory");
        $this->connection->query("DELETE FROM Resource_has_Funding_Reference");
        $this->connection->query("DELETE FROM Resource_has_Contact_Person");
        $this->connection->query("DELETE FROM Resource_has_Contributor_Person");
        $this->connection->query("DELETE FROM Resource_has_Contributor_Institution");
        $this->connection->query("DELETE FROM Resource_has_Author");
        $this->connection->query("DELETE FROM Resource_has_Free_Keywords");
        $this->connection->query("DELETE FROM Author_has_Affiliation");
        $this->connection->query("DELETE FROM Contact_Person_has_Affiliation");
        $this->connection->query("DELETE FROM Contributor_Person_has_Affiliation");
        $this->connection->query("DELETE FROM Contributor_Institution_has_Affiliation");
        $this->connection->query("DELETE FROM Originating_Laboratory_has_Affiliation");
        $this->connection->query("DELETE FROM Free_Keywords");
        $this->connection->query("DELETE FROM Affiliation");
        $this->connection->query("DELETE FROM Title");
        $this->connection->query("DELETE FROM Description");
        $this->connection->query("DELETE FROM Spatial_Temporal_Coverage");
        $this->connection->query("DELETE FROM Thesaurus_Keywords");
        $this->connection->query("DELETE FROM Related_Work");
        $this->connection->query("DELETE FROM Originating_Laboratory");
        $this->connection->query("DELETE FROM Funding_Reference");
        $this->connection->query("DELETE FROM Contact_Person");
        $this->connection->query("DELETE FROM Contributor_Person");
        $this->connection->query("DELETE FROM Contributor_Institution");
        $this->connection->query("DELETE FROM Author");
        $this->connection->query("DELETE FROM Resource");
        $this->connection->query("SET FOREIGN_KEY_CHECKS=1");
    }

    /**
     * Test saving of a single Originating Laboratory.
     */
    public function testSaveSingleOriginatingLaboratory()
    {
        $resourceData = [
            "doi" => "10.5880/GFZ.TEST.SINGLE.LAB",
            "year" => 2023,
            "dateCreated" => "2023-06-01",
            "resourcetype" => 1,
            "language" => 1,
            "Rights" => 1,
            "title" => ["Test Single Laboratory"],
            "titleType" => [1]
        ];
        $resource_id = saveResourceInformationAndRights($this->connection, $resourceData);

        $postData = [
            "laboratoryName" => ['Test Lab'],
            "LabId" => ["1b9abbf97c7caa2d763b647d476b2910"],
            "laboratoryAffiliation" => ['California Digital Library'],
            "laboratoryRorIds" => ['https://ror.org/03yrm5c26']
        ];

        $resultSave = saveOriginatingLaboratories($this->connection, $postData, $resource_id);
        $this->assertTrue($resultSave, "Speichern der Originating Laboratories fehlgeschlagen.");

        // Check if Originating Laboratory was saved correctly
        $stmt = $this->connection->prepare("SELECT * FROM Originating_Laboratory WHERE laboratoryname = ?");
        $labName = $postData["laboratoryName"][0];
        $stmt->bind_param("s", $labName);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        $this->assertNotNull($result, "Das Originating Laboratory wurde nicht gespeichert.");
        $this->assertEquals($labName, $result["laboratoryname"], "Der Laborname wurde nicht korrekt gespeichert.");
        $this->assertEquals($postData["LabId"][0], $result["labId"], "Die Lab ID wurde nicht korrekt gespeichert.");

        // Check if Originating Laboratory is linked to the Resource
        $stmt = $this->connection->prepare("SELECT * FROM Resource_has_Originating_Laboratory WHERE Resource_resource_id = ? AND Originating_Laboratory_originating_laboratory_id = ?");
        $stmt->bind_param("ii", $resource_id, $result["originating_laboratory_id"]);
        $stmt->execute();
        $this->assertEquals(1, $stmt->get_result()->num_rows, "Die Verknüpfung zur Resource wurde nicht korrekt erstellt.");

        // Check Affiliation
        $stmt = $this->connection->prepare("SELECT a.name, a.rorId FROM Affiliation a 
                                            JOIN Originating_Laboratory_has_Affiliation olha ON a.affiliation_id = olha.Affiliation_affiliation_id
                                            WHERE olha.Originating_Laboratory_originating_laboratory_id = ?");
        $stmt->bind_param("i", $result["originating_laboratory_id"]);
        $stmt->execute();
        $affiliationResult = $stmt->get_result()->fetch_assoc();

        $this->assertEquals($postData["laboratoryAffiliation"][0], $affiliationResult["name"], "Der Name der Affiliation wurde nicht korrekt gespeichert.");
        $this->assertEquals(str_replace("https://ror.org/", "", $postData["laboratoryRorIds"][0]), $affiliationResult["rorId"], "Die ROR-ID der Affiliation wurde nicht korrekt gespeichert.");
    }

    /**
     * Test saving of 3 Originating Laboratories.
     */
    public function testSaveMultipleOriginatingLaboratories()
    {
        $resourceData = [
            "doi" => "10.5880/GFZ.TEST.MULTIPLE.LABS",
            "year" => 2023,
            "dateCreated" => "2023-06-01",
            "resourcetype" => 1,
            "language" => 1,
            "Rights" => 1,
            "title" => ["Test Multiple Laboratories"],
            "titleType" => [1]
        ];
        $resource_id = saveResourceInformationAndRights($this->connection, $resourceData);

        $postData = [
            "laboratoryName" => ['Lab A', 'Lab B', 'Lab C'],
            "LabId" => [
                "1b9abbf97c7caa2d763b647d476b2910",
                "9cd562c216daa82792972a074a222c52",
                "09e434194091574963c80f83d586875d"
            ],
            "laboratoryAffiliation" => [
                'California Digital Library',
                'OurResearch',
                'University of California, San Diego'
            ],
            "laboratoryRorIds" => [
                'https://ror.org/03yrm5c26',
                'https://ror.org/02nr0ka47',
                'https://ror.org/0168r3w48'
            ]
        ];

        $resultSave = saveOriginatingLaboratories($this->connection, $postData, $resource_id);
        $this->assertTrue($resultSave, "Speichern der Originating Laboratories fehlgeschlagen.");

        // Check if Originating Laboratories were saved correctly
        for ($i = 0; $i < 3; $i++) {
            $stmt = $this->connection->prepare("SELECT * FROM Originating_Laboratory WHERE laboratoryname = ?");
            $labName = $postData["laboratoryName"][$i];
            $stmt->bind_param("s", $labName);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            $this->assertNotNull($result, "Das Originating Laboratory " . ($i + 1) . " wurde nicht gespeichert.");
            $this->assertEquals($labName, $result["laboratoryname"], "Der Laborname " . ($i + 1) . " wurde nicht korrekt gespeichert.");
            $this->assertEquals($postData["LabId"][$i], $result["labId"], "Die Lab ID " . ($i + 1) . " wurde nicht korrekt gespeichert.");

            // Check if Originating Laboratory is linked to the Resource
            $stmt = $this->connection->prepare("SELECT * FROM Resource_has_Originating_Laboratory WHERE Resource_resource_id = ? AND Originating_Laboratory_originating_laboratory_id = ?");
            $stmt->bind_param("ii", $resource_id, $result["originating_laboratory_id"]);
            $stmt->execute();
            $this->assertEquals(1, $stmt->get_result()->num_rows, "Die Verknüpfung zur Resource für Labor " . ($i + 1) . " wurde nicht korrekt erstellt.");

            // Check Affiliation
            $stmt = $this->connection->prepare("SELECT a.name, a.rorId FROM Affiliation a 
                                                JOIN Originating_Laboratory_has_Affiliation olha ON a.affiliation_id = olha.Affiliation_affiliation_id
                                                WHERE olha.Originating_Laboratory_originating_laboratory_id = ?");
            $stmt->bind_param("i", $result["originating_laboratory_id"]);
            $stmt->execute();
            $affiliationResult = $stmt->get_result()->fetch_assoc();

            $this->assertEquals($postData["laboratoryAffiliation"][$i], $affiliationResult["name"], "Der Name der Affiliation für Labor " . ($i + 1) . " wurde nicht korrekt gespeichert.");
            $this->assertEquals(str_replace("https://ror.org/", "", $postData["laboratoryRorIds"][$i]), $affiliationResult["rorId"], "Die ROR-ID der Affiliation für Labor " . ($i + 1) . " wurde nicht korrekt gespeichert.");
        }
    }
}