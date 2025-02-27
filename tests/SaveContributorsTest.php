<?php
namespace Tests;
use PHPUnit\Framework\TestCase;
use mysqli_sql_exception;

require_once __DIR__ . '/../settings.php';
require_once __DIR__ . '/../save/formgroups/save_contributorpersons.php';
require_once __DIR__ . '/../save/formgroups/save_contributorinstitutions.php';
require_once __DIR__ . '/TestDatabaseSetup.php';

/**
 * Test class for saving contributors functionality
 * 
 * Tests the saving of contributor persons and institutions with various scenarios 
 * including required fields, optional fields, and multiple contributors
 */
class SaveContributorsTest extends TestCase
{
    /**
     * @var \mysqli Database connection
     */
    private $connection;

    /**
     * Set up test environment
     * Creates test database if it doesn't exist and initializes database structure
     *
     * @return void
     * @throws \Exception If database setup fails
     */
    protected function setUp(): void
    {
        global $connection;
        if (!$connection) {
            $connection = connectDb();
        }
        $this->connection = $connection;

        $dbname = 'mde2-msl-test';
        try {
            if ($this->connection->select_db($dbname) === false) {
                $connection->query("CREATE DATABASE " . $dbname);
                $connection->select_db($dbname);
            }

            setupTestDatabase($connection);

        } catch (\Exception $e) {
            $this->fail("Fehler beim Setup der Testdatenbank: " . $e->getMessage());
        }
    }

    /**
     * Clean up test environment after each test
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->cleanupTestData();
    }

    /**
     * Remove all test data from database tables
     *
     * @return void
     */
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
     * Test saving a contributor person with all fields populated
     *
     * @return void
     */
    public function testSaveFullContributorPerson()
    {
        $resourceData = [
            "doi" => "10.5880/GFZ.TEST.FULL.PERSON",
            "year" => 2023,
            "dateCreated" => "2023-06-01",
            "resourcetype" => 1,
            "language" => 1,
            "Rights" => 1,
            "title" => ["Test Full Contributor Person"],
            "titleType" => [1]
        ];
        $resource_id = saveResourceInformationAndRights($this->connection, $resourceData);

        $postData = [
            "cbPersonLastname" => ["Doe"],
            "cbPersonFirstname" => ["John"],
            "cbORCID" => ["0000-0001-2345-6789"],
            "cbAffiliation" => ['[{"value":"Test University"}]'],
            "cbpRorIds" => ['https://ror.org/03yrm5c26'],
            "cbPersonRoles" => [["Data Collector", "Data Curator"]]
        ];

        saveContributorPersons($this->connection, $postData, $resource_id);

        $stmt = $this->connection->prepare("SELECT * FROM Contributor_Person WHERE orcid = ?");
        $stmt->bind_param("s", $postData["cbORCID"][0]);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        $this->assertNotNull($result, "Die Contributor Person wurde nicht gespeichert.");
        $this->assertEquals($postData["cbPersonLastname"][0], $result["familyname"], "Der Nachname wurde nicht korrekt gespeichert.");
        $this->assertEquals($postData["cbPersonFirstname"][0], $result["givenname"], "Der Vorname wurde nicht korrekt gespeichert.");

        $stmt = $this->connection->prepare("SELECT a.name, a.rorId FROM Affiliation a 
                                            JOIN Contributor_Person_has_Affiliation cpha ON a.affiliation_id = cpha.Affiliation_affiliation_id
                                            WHERE cpha.Contributor_Person_contributor_person_id = ?");
        $stmt->bind_param("i", $result["contributor_person_id"]);
        $stmt->execute();
        $affiliationResult = $stmt->get_result()->fetch_assoc();

        $this->assertEquals(json_decode($postData["cbAffiliation"][0], true)[0]["value"], $affiliationResult["name"], "Der Name der Affiliation wurde nicht korrekt gespeichert.");
        $this->assertEquals(
            str_replace("https://ror.org/", "", $postData["cbpRorIds"][0]),
            $affiliationResult["rorId"],
            "Die ROR-ID der Affiliation wurde nicht korrekt gespeichert."
        );

        $stmt = $this->connection->prepare("SELECT r.name FROM Role r 
                                            JOIN Contributor_Person_has_Role cphr ON r.role_id = cphr.Role_role_id
                                            WHERE cphr.Contributor_Person_contributor_person_id = ?");
        $stmt->bind_param("i", $result["contributor_person_id"]);
        $stmt->execute();
        $rolesResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $this->assertCount(2, $rolesResult, "Die Anzahl der gespeicherten Rollen stimmt nicht.");
        $this->assertEquals($postData["cbPersonRoles"][0], array_column($rolesResult, 'name'), "Die Rollen wurden nicht korrekt gespeichert.");
    }

    /**
     * Test saving a contributor institution with all fields populated
     *
     * @return void
     */
    public function testSaveFullContributorInstitution()
    {
        $resourceData = [
            "doi" => "10.5880/GFZ.TEST.FULL.INSTITUTION",
            "year" => 2023,
            "dateCreated" => "2023-06-01",
            "resourcetype" => 1,
            "language" => 1,
            "Rights" => 1,
            "title" => ["Test Full Contributor Institution"],
            "titleType" => [1]
        ];
        $resource_id = saveResourceInformationAndRights($this->connection, $resourceData);

        $postData = [
            "cbOrganisationName" => ["Test Organization"],
            "cbOrganisationRoles" => [["Hosting Institution", "Research Group"]],
            "OrganisationAffiliation" => ['[{"value":"Test Affiliation"}]'],
            "hiddenOrganisationRorId" => ['https://ror.org/03yrm5c26']
        ];

        saveContributorInstitutions($this->connection, $postData, $resource_id);

        $stmt = $this->connection->prepare("SELECT * FROM Contributor_Institution WHERE name = ?");
        $stmt->bind_param("s", $postData["cbOrganisationName"][0]);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        $this->assertNotNull($result, "Die Contributor Institution wurde nicht gespeichert.");
        $this->assertEquals($postData["cbOrganisationName"][0], $result["name"], "Der Name der Institution wurde nicht korrekt gespeichert.");

        $stmt = $this->connection->prepare("SELECT a.name, a.rorId FROM Affiliation a 
                                            JOIN Contributor_Institution_has_Affiliation ciha ON a.affiliation_id = ciha.Affiliation_affiliation_id
                                            WHERE ciha.Contributor_Institution_contributor_institution_id = ?");
        $stmt->bind_param("i", $result["contributor_institution_id"]);
        $stmt->execute();
        $affiliationResult = $stmt->get_result()->fetch_assoc();

        $this->assertEquals(json_decode($postData["OrganisationAffiliation"][0], true)[0]["value"], $affiliationResult["name"], "Der Name der Affiliation wurde nicht korrekt gespeichert.");
        $this->assertEquals(
            str_replace("https://ror.org/", "", $postData["hiddenOrganisationRorId"][0]),
            $affiliationResult["rorId"],
            "Die ROR-ID der Affiliation wurde nicht korrekt gespeichert."
        );

        $stmt = $this->connection->prepare("SELECT r.name FROM Role r 
                                            JOIN Contributor_Institution_has_Role cihr ON r.role_id = cihr.Role_role_id
                                            WHERE cihr.Contributor_Institution_contributor_institution_id = ?");
        $stmt->bind_param("i", $result["contributor_institution_id"]);
        $stmt->execute();
        $rolesResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $this->assertCount(2, $rolesResult, "Die Anzahl der gespeicherten Rollen stimmt nicht.");
        $this->assertEquals($postData["cbOrganisationRoles"][0], array_column($rolesResult, 'name'), "Die Rollen wurden nicht korrekt gespeichert.");
    }

    /**
     * Test saving a contributor person with only required fields
     *
     * @return void
     */
    public function testSaveContributorPersonRequiredFieldsOnly()
    {
        $resourceData = [
            "doi" => "10.5880/GFZ.TEST.PERSON.REQUIRED",
            "year" => 2023,
            "dateCreated" => "2023-06-01",
            "resourcetype" => 1,
            "language" => 1,
            "Rights" => 1,
            "title" => ["Test Contributor Person Required Fields"],
            "titleType" => [1]
        ];
        $resource_id = saveResourceInformationAndRights($this->connection, $resourceData);

        $postData = [
            "cbPersonLastname" => ["Doe"],
            "cbPersonFirstname" => [""],
            "cbORCID" => ["0000-0001-2345-6789"],
            "cbAffiliation" => ['[]'],
            "cbpRorIds" => ['[]'],
            "cbPersonRoles" => [["Data Collector"]]
        ];

        saveContributorPersons($this->connection, $postData, $resource_id);

        $stmt = $this->connection->prepare("SELECT * FROM Contributor_Person WHERE orcid = ?");
        $stmt->bind_param("s", $postData["cbORCID"][0]);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        $this->assertNotNull($result, "Die Contributor Person wurde nicht gespeichert.");
        $this->assertEquals($postData["cbPersonLastname"][0], $result["familyname"], "Der Nachname wurde nicht korrekt gespeichert.");
        $this->assertEquals($postData["cbPersonFirstname"][0], $result["givenname"], "Der Vorname wurde nicht korrekt gespeichert.");

        $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM Contributor_Person_has_Affiliation WHERE Contributor_Person_contributor_person_id = ?");
        $stmt->bind_param("i", $result["contributor_person_id"]);
        $stmt->execute();
        $affiliationCount = $stmt->get_result()->fetch_assoc()['count'];
        $this->assertEquals(0, $affiliationCount, "Es sollte keine Affiliation gespeichert worden sein.");

        $stmt = $this->connection->prepare("SELECT r.name FROM Role r 
                                            JOIN Contributor_Person_has_Role cphr ON r.role_id = cphr.Role_role_id
                                            WHERE cphr.Contributor_Person_contributor_person_id = ?");
        $stmt->bind_param("i", $result["contributor_person_id"]);
        $stmt->execute();
        $rolesResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $this->assertCount(1, $rolesResult, "Die Anzahl der gespeicherten Rollen stimmt nicht.");
        $this->assertEquals($postData["cbPersonRoles"][0], array_column($rolesResult, 'name'), "Die Rolle wurde nicht korrekt gespeichert.");
    }

    /**
     * Test saving incomplete contributor person and institution
     * Verifies that incomplete records are not saved
     *
     * @return void
     */
    public function testSaveIncompleteContributors()
    {
        $resourceData = [
            "doi" => "10.5880/GFZ.TEST.INCOMPLETE",
            "year" => 2023,
            "dateCreated" => "2023-06-01",
            "resourcetype" => 1,
            "language" => 1,
            "Rights" => 1,
            "title" => ["Test Incomplete Contributors"],
            "titleType" => [1]
        ];
        $resource_id = saveResourceInformationAndRights($this->connection, $resourceData);

        $postData = [
            "cbPersonLastname" => [""],
            "cbPersonFirstname" => ["John"],
            "cbORCID" => ["0000-0001-2345-6789"],
            "cbAffiliation" => ['[{"value":"Test University"}]'],
            "cbpRorIds" => ['[{"value":"https://ror.org/03yrm5c26"}]'],
            "cbPersonRoles" => [["Data Collector"]],
            "cbOrganisationName" => ["Test Organization"],
            "cbOrganisationRoles" => [[]],
            "OrganisationAffiliation" => ['[{"value":"Test Affiliation"}]'],
            "hiddenOrganisationRorId" => ['https://ror.org/03yrm5c26']
        ];

        saveContributorPersons($this->connection, $postData, $resource_id);
        saveContributorInstitutions($this->connection, $postData, $resource_id);

        $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM Contributor_Person");
        $stmt->execute();
        $personCount = $stmt->get_result()->fetch_assoc()['count'];
        $this->assertEquals(1, $personCount, "Es sollte keine Contributor Person gespeichert worden sein.");

        $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM Contributor_Institution");
        $stmt->execute();
        $institutionCount = $stmt->get_result()->fetch_assoc()['count'];
        $this->assertEquals(0, $institutionCount, "Es sollte keine Contributor Institution gespeichert worden sein.");
    }

    /**
     * Test saving multiple contributor persons
     * Verifies correct handling of multiple person entries including affiliations and roles
     *
     * @return void
     */
    public function testSaveMultipleContributorPersons()
    {
        $resourceData = [
            "doi" => "10.5880/GFZ.TEST.MULTIPLE.PERSONS",
            "year" => 2023,
            "dateCreated" => "2023-06-01",
            "resourcetype" => 1,
            "language" => 1,
            "Rights" => 1,
            "title" => ["Test Multiple Contributor Persons"],
            "titleType" => [1]
        ];
        $resource_id = saveResourceInformationAndRights($this->connection, $resourceData);

        $postData = [
            "cbPersonLastname" => ["Doe", "Smith", "Johnson"],
            "cbPersonFirstname" => ["John", "Jane", "Bob"],
            "cbORCID" => ["0000-0001-2345-6789", "0000-0002-3456-7890", "0000-0003-4567-8901"],
            "cbAffiliation" => ['[{"value":"University A"}]', '[{"value":"University B"}]', '[{"value":"University C"}]'],
            "cbpRorIds" => ['https://ror.org/03yrm5c26', 'https://ror.org/02nr0ka47', 'https://ror.org/0168r3w48'],
            "cbPersonRoles" => [["Data Collector"], ["Data Curator"], ["Researcher"]]
        ];

        saveContributorPersons($this->connection, $postData, $resource_id);

        for ($i = 0; $i < 3; $i++) {
            $stmt = $this->connection->prepare("SELECT * FROM Contributor_Person WHERE orcid = ?");
            $stmt->bind_param("s", $postData["cbORCID"][$i]);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            $this->assertNotNull($result, "Die Contributor Person " . ($i + 1) . " wurde nicht gespeichert.");
            $this->assertEquals($postData["cbPersonLastname"][$i], $result["familyname"], "Der Nachname der Person " . ($i + 1) . " wurde nicht korrekt gespeichert.");
            $this->assertEquals($postData["cbPersonFirstname"][$i], $result["givenname"], "Der Vorname der Person " . ($i + 1) . " wurde nicht korrekt gespeichert.");

            $stmt = $this->connection->prepare("SELECT a.name, a.rorId FROM Affiliation a 
                                                JOIN Contributor_Person_has_Affiliation cpha ON a.affiliation_id = cpha.Affiliation_affiliation_id
                                                WHERE cpha.Contributor_Person_contributor_person_id = ?");
            $stmt->bind_param("i", $result["contributor_person_id"]);
            $stmt->execute();
            $affiliationResult = $stmt->get_result()->fetch_assoc();

            $this->assertEquals(json_decode($postData["cbAffiliation"][$i], true)[0]["value"], $affiliationResult["name"], "Der Name der Affiliation für Person " . ($i + 1) . " wurde nicht korrekt gespeichert.");
            $this->assertEquals(
                str_replace("https://ror.org/", "", $postData["cbpRorIds"][$i]),
                $affiliationResult["rorId"],
                "Die ROR-ID der Affiliation für Person " . ($i + 1) . " wurde nicht korrekt gespeichert."
            );

            $stmt = $this->connection->prepare("SELECT r.name FROM Role r 
                                                JOIN Contributor_Person_has_Role cphr ON r.role_id = cphr.Role_role_id
                                                WHERE cphr.Contributor_Person_contributor_person_id = ?");
            $stmt->bind_param("i", $result["contributor_person_id"]);
            $stmt->execute();
            $rolesResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $this->assertCount(1, $rolesResult, "Die Anzahl der gespeicherten Rollen für Person " . ($i + 1) . " stimmt nicht.");
            $this->assertEquals($postData["cbPersonRoles"][$i], array_column($rolesResult, 'name'), "Die Rolle für Person " . ($i + 1) . " wurde nicht korrekt gespeichert.");
        }
    }

    /**
     * Test saving multiple contributor institutions
     * Verifies correct handling of multiple institution entries including affiliations and roles
     *
     * @return void
     */
    public function testSaveMultipleContributorInstitutions()
    {
        $resourceData = [
            "doi" => "10.5880/GFZ.TEST.MULTIPLE.INSTITUTIONS",
            "year" => 2023,
            "dateCreated" => "2023-06-01",
            "resourcetype" => 1,
            "language" => 1,
            "Rights" => 1,
            "title" => ["Test Multiple Contributor Institutions"],
            "titleType" => [1]
        ];
        $resource_id = saveResourceInformationAndRights($this->connection, $resourceData);

        $postData = [
            "cbOrganisationName" => ["Organization A", "Organization B", "Organization C"],
            "cbOrganisationRoles" => [["Hosting Institution"], ["Research Group"], ["Sponsor"]],
            "OrganisationAffiliation" => ['[{"value":"Affiliation A"}]', '[{"value":"Affiliation B"}]', '[{"value":"Affiliation C"}]'],
            "hiddenOrganisationRorId" => ['https://ror.org/03yrm5c26', 'https://ror.org/02nr0ka47', 'https://ror.org/0168r3w48']
        ];

        saveContributorInstitutions($this->connection, $postData, $resource_id);

        for ($i = 0; $i < 3; $i++) {
            $stmt = $this->connection->prepare("SELECT * FROM Contributor_Institution WHERE name = ?");
            $stmt->bind_param("s", $postData["cbOrganisationName"][$i]);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            $this->assertNotNull($result, "Die Contributor Institution " . ($i + 1) . " wurde nicht gespeichert.");
            $this->assertEquals($postData["cbOrganisationName"][$i], $result["name"], "Der Name der Institution " . ($i + 1) . " wurde nicht korrekt gespeichert.");

            $stmt = $this->connection->prepare("SELECT a.name, a.rorId FROM Affiliation a 
                                                JOIN Contributor_Institution_has_Affiliation ciha ON a.affiliation_id = ciha.Affiliation_affiliation_id
                                                WHERE ciha.Contributor_Institution_contributor_institution_id = ?");
            $stmt->bind_param("i", $result["contributor_institution_id"]);
            $stmt->execute();
            $affiliationResult = $stmt->get_result()->fetch_assoc();

            $this->assertEquals(json_decode($postData["OrganisationAffiliation"][$i], true)[0]["value"], $affiliationResult["name"], "Der Name der Affiliation für Institution " . ($i + 1) . " wurde nicht korrekt gespeichert.");
            $this->assertEquals(
                str_replace("https://ror.org/", "", $postData["hiddenOrganisationRorId"][$i]),
                $affiliationResult["rorId"],
                "Die ROR-ID der Affiliation für Institution " . ($i + 1) . " wurde nicht korrekt gespeichert."
            );

            $stmt = $this->connection->prepare("SELECT r.name FROM Role r 
                                                JOIN Contributor_Institution_has_Role cihr ON r.role_id = cihr.Role_role_id
                                                WHERE cihr.Contributor_Institution_contributor_institution_id = ?");
            $stmt->bind_param("i", $result["contributor_institution_id"]);
            $stmt->execute();
            $rolesResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $this->assertCount(1, $rolesResult, "Die Anzahl der gespeicherten Rollen für Institution " . ($i + 1) . " stimmt nicht.");
            $this->assertEquals($postData["cbOrganisationRoles"][$i], array_column($rolesResult, 'name'), "Die Rolle für Institution " . ($i + 1) . " wurde nicht korrekt gespeichert.");
        }
    }

    /**
     * Test saving multiple contributor persons and institutions simultaneously
     * Verifies correct handling of mixed contributor types in a single save operation
     *
     * @return void
     */
    public function testSaveMultipleContributorPersonsAndInstitutions()
    {
        $resourceData = [
            "doi" => "10.5880/GFZ.TEST.MULTIPLE.MIXED",
            "year" => 2023,
            "dateCreated" => "2023-06-01",
            "resourcetype" => 1,
            "language" => 1,
            "Rights" => 1,
            "title" => ["Test Multiple Mixed Contributors"],
            "titleType" => [1]
        ];
        $resource_id = saveResourceInformationAndRights($this->connection, $resourceData);

        $postData = [
            "cbPersonLastname" => ["Doe", "Smith"],
            "cbPersonFirstname" => ["John", "Jane"],
            "cbORCID" => ["0000-0001-2345-6789", "0000-0002-3456-7890"],
            "cbAffiliation" => ['[{"value":"University A"}]', '[{"value":"University B"}]'],
            "cbpRorIds" => ['[{"value":"https://ror.org/03yrm5c26"}]', '[{"value":"https://ror.org/02nr0ka47"}]'],
            "cbPersonRoles" => [["Data Collector"], ["Data Curator"]],
            "cbOrganisationName" => ["Organization A", "Organization B"],
            "cbOrganisationRoles" => [["Hosting Institution"], ["Research Group"]],
            "OrganisationAffiliation" => ['[{"value":"Affiliation A"}]', '[{"value":"Affiliation B"}]'],
            "hiddenOrganisationRorId" => ['https://ror.org/0168r3w48', 'https://ror.org/04m7fg108']
        ];

        saveContributorPersons($this->connection, $postData, $resource_id);
        saveContributorInstitutions($this->connection, $postData, $resource_id);

        for ($i = 0; $i < 2; $i++) {
            $stmt = $this->connection->prepare("SELECT * FROM Contributor_Person WHERE orcid = ?");
            $stmt->bind_param("s", $postData["cbORCID"][$i]);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            $this->assertNotNull($result, "Die Contributor Person " . ($i + 1) . " wurde nicht gespeichert.");
            $this->assertEquals($postData["cbPersonLastname"][$i], $result["familyname"], "Der Nachname der Person " . ($i + 1) . " wurde nicht korrekt gespeichert.");
            $this->assertEquals($postData["cbPersonFirstname"][$i], $result["givenname"], "Der Vorname der Person " . ($i + 1) . " wurde nicht korrekt gespeichert.");
        }

        for ($i = 0; $i < 2; $i++) {
            $stmt = $this->connection->prepare("SELECT * FROM Contributor_Institution WHERE name = ?");
            $stmt->bind_param("s", $postData["cbOrganisationName"][$i]);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            $this->assertNotNull($result, "Die Contributor Institution " . ($i + 1) . " wurde nicht gespeichert.");
            $this->assertEquals($postData["cbOrganisationName"][$i], $result["name"], "Der Name der Institution " . ($i + 1) . " wurde nicht korrekt gespeichert.");
        }
    }
}