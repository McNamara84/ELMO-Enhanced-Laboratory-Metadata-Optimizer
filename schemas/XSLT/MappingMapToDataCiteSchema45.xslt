<?xml version="1.0" encoding="UTF-8"?>
<!--
This file was generated by Altova MapForce 2024

YOU SHOULD NOT MODIFY THIS FILE, BECAUSE IT WILL BE
OVERWRITTEN WHEN YOU RE-RUN CODE GENERATION.

Refer to the Altova MapForce Documentation for further details.
http://www.altova.com/mapforce
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xs="http://www.w3.org/2001/XMLSchema" exclude-result-prefixes="xs">
	<xsl:output method="xml" encoding="UTF-8" indent="yes"/>
	<xsl:template match="/">
		<xsl:variable name="var1_initial" select="."/>
		<resource xmlns="http://datacite.org/schema/kernel-4">
			<xsl:attribute name="xsi:schemaLocation" namespace="http://www.w3.org/2001/XMLSchema-instance">http://datacite.org/schema/kernel-4 file:///C:/xampp/htdocs/msl-mde/schemas/DataCite/DataCiteSchema45.xsd</xsl:attribute>
			<xsl:for-each select="*[local-name()='Resource' and namespace-uri()='']">
				<xsl:variable name="var2_cur" select="."/>
				<identifier>
					<xsl:attribute name="identifierType" namespace="">DOI</xsl:attribute>
					<xsl:value-of select="*[local-name()='doi' and namespace-uri()='']"/>
				</identifier>
				<creators>
					<xsl:for-each select="*[local-name()='Authors' and namespace-uri()='']/*[local-name()='Author' and namespace-uri()='']">
						<xsl:variable name="var3_cur" select="."/>
						<creator>
							<creatorName>
								<xsl:attribute name="nameType" namespace="">Personal</xsl:attribute>
								<xsl:value-of select="concat(*[local-name()='familyname' and namespace-uri()=''], ', ', *[local-name()='givenname' and namespace-uri()=''])"/>
							</creatorName>
							<givenName>
								<xsl:value-of select="*[local-name()='givenname' and namespace-uri()='']"/>
							</givenName>
							<familyName>
								<xsl:value-of select="*[local-name()='familyname' and namespace-uri()='']"/>
							</familyName>
							<xsl:for-each select="(./*[local-name()='orcid' and namespace-uri()=''])[((string-length(string(.)) &gt; 0) and boolean($var3_cur/*[local-name()='orcid' and namespace-uri()='']))]">
								<xsl:variable name="var4_filter" select="."/>
								<nameIdentifier>
									<xsl:attribute name="nameIdentifierScheme" namespace="">ORCID</xsl:attribute>
									<xsl:attribute name="schemeURI" namespace="">https://orcid.org/</xsl:attribute>
									<xsl:value-of select="."/>
								</nameIdentifier>
							</xsl:for-each>
							<xsl:for-each select="*[local-name()='Affiliations' and namespace-uri()='']/*[local-name()='Affiliation' and namespace-uri()='']">
								<xsl:variable name="var5_cur" select="."/>
								<affiliation>
									<xsl:if test="*[local-name()='rorId' and namespace-uri()='']">
										<xsl:attribute name="affiliationIdentifierScheme" namespace="">ROR</xsl:attribute>
									</xsl:if>
									<xsl:if test="*[local-name()='rorId' and namespace-uri()='']">
										<xsl:attribute name="schemeURI" namespace="">https://ror.org/</xsl:attribute>
									</xsl:if>
									<xsl:if test="*[local-name()='rorId' and namespace-uri()='']">
										<xsl:for-each select="*[local-name()='rorId' and namespace-uri()='']">
											<xsl:variable name="var6_cur" select="."/>
											<xsl:attribute name="affiliationIdentifier" namespace="">
												<xsl:value-of select="concat('https://ror.org/', .)"/>
											</xsl:attribute>
										</xsl:for-each>
									</xsl:if>
									<xsl:value-of select="*[local-name()='name' and namespace-uri()='']"/>
								</affiliation>
							</xsl:for-each>
						</creator>
					</xsl:for-each>
				</creators>
				<titles>
					<xsl:for-each select="*[local-name()='Titles' and namespace-uri()='']/*[local-name()='Title' and namespace-uri()='']">
						<xsl:variable name="var7_cur" select="."/>
						<title>
							<xsl:if test="not(contains(*[local-name()='type' and namespace-uri()=''], 'Main Title'))">
								<xsl:attribute name="titleType" namespace="">
									<xsl:value-of select="concat(substring-before(*[local-name()='type' and namespace-uri()=''], ' '), substring-after(*[local-name()='type' and namespace-uri()=''], ' '))"/>
								</xsl:attribute>
							</xsl:if>
							<xsl:if test="contains(*[local-name()='type' and namespace-uri()=''], 'Main Title')">
								<xsl:attribute name="xml:lang">en</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="*[local-name()='text' and namespace-uri()='']"/>
						</title>
					</xsl:for-each>
				</titles>
				<publisher>
					<xsl:attribute name="xml:lang">en</xsl:attribute>
					<xsl:value-of select="'GFZ Data Services'"/>
				</publisher>
				<publicationYear>
					<xsl:value-of select="*[local-name()='year' and namespace-uri()='']"/>
				</publicationYear>
				<resourceType>
					<xsl:attribute name="resourceTypeGeneral" namespace="">
						<xsl:value-of select="*[local-name()='ResourceType' and namespace-uri()='']/*[local-name()='resource_type_general' and namespace-uri()='']"/>
					</xsl:attribute>
					<xsl:value-of select="*[local-name()='ResourceType' and namespace-uri()='']/*[local-name()='resource_type_general' and namespace-uri()='']"/>
				</resourceType>
				<subjects>
					<xsl:for-each select="*[local-name()='ThesaurusKeywords' and namespace-uri()='']/*[local-name()='Keyword' and namespace-uri()='']">
						<xsl:variable name="var8_cur" select="."/>
						<subject>
							<xsl:attribute name="subjectScheme" namespace="">
								<xsl:value-of select="*[local-name()='scheme' and namespace-uri()='']"/>
							</xsl:attribute>
							<xsl:attribute name="schemeURI" namespace="">
								<xsl:value-of select="*[local-name()='schemeURI' and namespace-uri()='']"/>
							</xsl:attribute>
							<xsl:attribute name="valueURI" namespace="">
								<xsl:value-of select="*[local-name()='valueURI' and namespace-uri()='']"/>
							</xsl:attribute>
							<xsl:attribute name="xml:lang">
								<xsl:value-of select="*[local-name()='language' and namespace-uri()='']"/>
							</xsl:attribute>
							<xsl:value-of select="*[local-name()='keyword' and namespace-uri()='']"/>
						</subject>
					</xsl:for-each>
					<xsl:for-each select="*[local-name()='FreeKeywords' and namespace-uri()='']/*[local-name()='Keyword' and namespace-uri()='']">
						<xsl:variable name="var9_cur" select="."/>
						<subject>
							<xsl:value-of select="*[local-name()='free_keyword' and namespace-uri()='']"/>
						</subject>
					</xsl:for-each>
				</subjects>
				<contributors>
					<xsl:for-each select="*[local-name()='ContactPersons' and namespace-uri()='']/*[local-name()='ContactPerson' and namespace-uri()='']">
						<xsl:variable name="var10_cur" select="."/>
						<contributor>
							<xsl:attribute name="contributorType" namespace="">ContactPerson</xsl:attribute>
							<contributorName>
								<xsl:value-of select="concat(*[local-name()='familyname' and namespace-uri()=''], ', ', *[local-name()='givenname' and namespace-uri()=''])"/>
							</contributorName>
							<givenName>
								<xsl:value-of select="*[local-name()='givenname' and namespace-uri()='']"/>
							</givenName>
							<familyName>
								<xsl:value-of select="*[local-name()='familyname' and namespace-uri()='']"/>
							</familyName>
							<xsl:for-each select="*[local-name()='orcid' and namespace-uri()='']">
								<xsl:variable name="var11_cur" select="."/>
								<nameIdentifier>
									<xsl:attribute name="nameIdentifierScheme" namespace="">ORCID</xsl:attribute>
									<xsl:attribute name="schemeURI" namespace="">https://orcid.org/</xsl:attribute>
									<xsl:value-of select="."/>
								</nameIdentifier>
							</xsl:for-each>
							<xsl:for-each select="*[local-name()='Affiliations' and namespace-uri()='']/*[local-name()='Affiliation' and namespace-uri()='']">
								<xsl:variable name="var12_cur" select="."/>
								<affiliation>
									<xsl:if test="*[local-name()='rorId' and namespace-uri()='']">
										<xsl:attribute name="affiliationIdentifierScheme" namespace="">ROR</xsl:attribute>
									</xsl:if>
									<xsl:if test="*[local-name()='rorId' and namespace-uri()='']">
										<xsl:attribute name="schemeURI" namespace="">https://ror.org</xsl:attribute>
									</xsl:if>
									<xsl:if test="*[local-name()='rorId' and namespace-uri()='']">
										<xsl:for-each select="*[local-name()='rorId' and namespace-uri()='']">
											<xsl:variable name="var13_cur" select="."/>
											<xsl:attribute name="affiliationIdentifier" namespace="">
												<xsl:value-of select="concat('https://ror.org/', .)"/>
											</xsl:attribute>
										</xsl:for-each>
									</xsl:if>
									<xsl:value-of select="*[local-name()='name' and namespace-uri()='']"/>
								</affiliation>
							</xsl:for-each>
						</contributor>
					</xsl:for-each>
					<xsl:for-each select="*[local-name()='Contributors' and namespace-uri()='']/*[local-name()='Persons' and namespace-uri()='']/*[local-name()='Person' and namespace-uri()='']">
						<xsl:variable name="var14_cur" select="."/>
						<xsl:for-each select="*[local-name()='Roles' and namespace-uri()='']/*[local-name()='Role' and namespace-uri()='']">
							<xsl:variable name="var15_cur" select="."/>
							<contributor>
								<xsl:attribute name="contributorType" namespace="">
									<xsl:choose>
										<xsl:when test="contains(*[local-name()='name' and namespace-uri()=''], ' ')">
											<xsl:value-of select="concat(substring-before(*[local-name()='name' and namespace-uri()=''], ' '), substring-after(*[local-name()='name' and namespace-uri()=''], ' '))"/>
										</xsl:when>
										<xsl:otherwise>
											<xsl:value-of select="*[local-name()='name' and namespace-uri()='']"/>
										</xsl:otherwise>
									</xsl:choose>
								</xsl:attribute>
								<contributorName>
									<xsl:attribute name="nameType" namespace="">Personal</xsl:attribute>
									<xsl:value-of select="concat($var14_cur/*[local-name()='familyname' and namespace-uri()=''], ', ', $var14_cur/*[local-name()='givenname' and namespace-uri()=''])"/>
								</contributorName>
								<givenName>
									<xsl:value-of select="$var14_cur/*[local-name()='givenname' and namespace-uri()='']"/>
								</givenName>
								<familyName>
									<xsl:value-of select="$var14_cur/*[local-name()='familyname' and namespace-uri()='']"/>
								</familyName>
								<xsl:if test="(true() and (string-length(string($var14_cur/*[local-name()='orcid' and namespace-uri()=''])) &gt; 0))">
									<nameIdentifier>
										<xsl:attribute name="nameIdentifierScheme" namespace="">ORCID</xsl:attribute>
										<xsl:attribute name="schemeURI" namespace="">https://orcid.org/</xsl:attribute>
										<xsl:value-of select="$var14_cur/*[local-name()='orcid' and namespace-uri()='']"/>
									</nameIdentifier>
								</xsl:if>
								<xsl:for-each select="$var14_cur/*[local-name()='Affiliations' and namespace-uri()='']/*[local-name()='Affiliation' and namespace-uri()='']">
									<xsl:variable name="var16_cur" select="."/>
									<affiliation>
										<xsl:if test="*[local-name()='rorId' and namespace-uri()='']">
											<xsl:attribute name="affiliationIdentifierScheme" namespace="">ROR</xsl:attribute>
										</xsl:if>
										<xsl:if test="*[local-name()='rorId' and namespace-uri()='']">
											<xsl:attribute name="schemeURI" namespace="">https://ror.org</xsl:attribute>
										</xsl:if>
										<xsl:for-each select="*[local-name()='rorId' and namespace-uri()='']">
											<xsl:variable name="var17_cur" select="."/>
											<xsl:attribute name="affiliationIdentifier" namespace="">
												<xsl:value-of select="concat('https://ror.org/', .)"/>
											</xsl:attribute>
										</xsl:for-each>
										<xsl:value-of select="*[local-name()='name' and namespace-uri()='']"/>
									</affiliation>
								</xsl:for-each>
							</contributor>
						</xsl:for-each>
					</xsl:for-each>
					<xsl:for-each select="*[local-name()='Contributors' and namespace-uri()='']/*[local-name()='Institutions' and namespace-uri()='']/*[local-name()='Institution' and namespace-uri()='']">
						<xsl:variable name="var18_cur" select="."/>
						<xsl:for-each select="*[local-name()='Roles' and namespace-uri()='']/*[local-name()='Role' and namespace-uri()='']">
							<xsl:variable name="var19_cur" select="."/>
							<contributor>
								<xsl:attribute name="contributorType" namespace="">
									<xsl:choose>
										<xsl:when test="contains(*[local-name()='name' and namespace-uri()=''], ' ')">
											<xsl:value-of select="concat(substring-before(*[local-name()='name' and namespace-uri()=''], ' '), substring-after(*[local-name()='name' and namespace-uri()=''], ' '))"/>
										</xsl:when>
										<xsl:otherwise>
											<xsl:value-of select="*[local-name()='name' and namespace-uri()='']"/>
										</xsl:otherwise>
									</xsl:choose>
								</xsl:attribute>
								<contributorName>
									<xsl:attribute name="nameType" namespace="">Organizational</xsl:attribute>
									<xsl:value-of select="$var18_cur/*[local-name()='name' and namespace-uri()='']"/>
								</contributorName>
								<xsl:for-each select="$var18_cur/*[local-name()='Affiliations' and namespace-uri()='']/*[local-name()='Affiliation' and namespace-uri()='']">
									<xsl:variable name="var20_cur" select="."/>
									<affiliation>
										<xsl:if test="*[local-name()='rorId' and namespace-uri()='']">
											<xsl:attribute name="affiliationIdentifierScheme" namespace="">ROR</xsl:attribute>
										</xsl:if>
										<xsl:if test="*[local-name()='rorId' and namespace-uri()='']">
											<xsl:attribute name="schemeURI" namespace="">https://ror.org</xsl:attribute>
										</xsl:if>
										<xsl:for-each select="*[local-name()='rorId' and namespace-uri()='']">
											<xsl:variable name="var21_cur" select="."/>
											<xsl:attribute name="affiliationIdentifier" namespace="">
												<xsl:value-of select="concat('https://ror.org/', .)"/>
											</xsl:attribute>
										</xsl:for-each>
										<xsl:value-of select="*[local-name()='name' and namespace-uri()='']"/>
									</affiliation>
								</xsl:for-each>
							</contributor>
						</xsl:for-each>
					</xsl:for-each>
					<xsl:for-each select="*[local-name()='OriginatingLaboratories' and namespace-uri()='']/*[local-name()='OriginatingLaboratory' and namespace-uri()='']">
						<xsl:variable name="var22_cur" select="."/>
						<contributor>
							<xsl:attribute name="contributorType" namespace="">HostingInstitution</xsl:attribute>
							<contributorName>
								<xsl:value-of select="*[local-name()='laboratoryname' and namespace-uri()='']"/>
							</contributorName>
							<nameIdentifier>
								<xsl:attribute name="nameIdentifierScheme" namespace="">labid</xsl:attribute>
								<xsl:value-of select="*[local-name()='labId' and namespace-uri()='']"/>
							</nameIdentifier>
							<xsl:for-each select="*[local-name()='Affiliations' and namespace-uri()='']/*[local-name()='Affiliation' and namespace-uri()='']">
								<xsl:variable name="var23_cur" select="."/>
								<affiliation>
									<xsl:if test="*[local-name()='rorId' and namespace-uri()='']">
										<xsl:attribute name="affiliationIdentifierScheme" namespace="">ROR</xsl:attribute>
									</xsl:if>
									<xsl:if test="*[local-name()='rorId' and namespace-uri()='']">
										<xsl:attribute name="schemeURI" namespace="">https://ror.org</xsl:attribute>
									</xsl:if>
									<xsl:for-each select="*[local-name()='rorId' and namespace-uri()='']">
										<xsl:variable name="var24_cur" select="."/>
										<xsl:attribute name="affiliationIdentifier" namespace="">
											<xsl:value-of select="concat('https://ror.org/', .)"/>
										</xsl:attribute>
									</xsl:for-each>
									<xsl:value-of select="*[local-name()='name' and namespace-uri()='']"/>
								</affiliation>
							</xsl:for-each>
						</contributor>
					</xsl:for-each>
				</contributors>
				<dates>
					<xsl:for-each select="*[local-name()='dateEmbargoUntil' and namespace-uri()='']">
						<xsl:variable name="var25_cur" select="."/>
						<date>
							<xsl:attribute name="dateType" namespace="">Available</xsl:attribute>
							<xsl:value-of select="."/>
						</date>
					</xsl:for-each>
					<xsl:for-each select="*[local-name()='SpatialTemporalCoverages' and namespace-uri()='']/*[local-name()='SpatialTemporalCoverage' and namespace-uri()='']">
						<xsl:variable name="var26_cur" select="."/>
						<xsl:for-each select="*[local-name()='dateStart' and namespace-uri()='']">
							<xsl:variable name="var27_cur" select="."/>
							<xsl:variable name="var28_nested">
								<xsl:for-each select="$var2_cur/*[local-name()='SpatialTemporalCoverages' and namespace-uri()='']/*[local-name()='SpatialTemporalCoverage' and namespace-uri()='']">
									<xsl:variable name="var29_cur" select="."/>
									<xsl:value-of select="number(boolean(*[local-name()='timeStart' and namespace-uri()='']))"/>
								</xsl:for-each>
							</xsl:variable>
							<xsl:choose>
								<xsl:when test="boolean(translate(normalize-space($var28_nested), ' 0', ''))">
									<xsl:for-each select="$var26_cur/*[local-name()='timeStart' and namespace-uri()='']">
										<xsl:variable name="var30_cur" select="."/>
										<xsl:for-each select="$var26_cur/*[local-name()='timezone' and namespace-uri()='']">
											<xsl:variable name="var31_cur" select="."/>
											<xsl:for-each select="$var26_cur/*[local-name()='dateEnd' and namespace-uri()='']">
												<xsl:variable name="var32_cur" select="."/>
												<xsl:variable name="var33_nested">
													<xsl:for-each select="$var2_cur/*[local-name()='SpatialTemporalCoverages' and namespace-uri()='']/*[local-name()='SpatialTemporalCoverage' and namespace-uri()='']">
														<xsl:variable name="var34_cur" select="."/>
														<xsl:value-of select="number(boolean(*[local-name()='timeEnd' and namespace-uri()='']))"/>
													</xsl:for-each>
												</xsl:variable>
												<xsl:choose>
													<xsl:when test="boolean(translate(normalize-space($var33_nested), ' 0', ''))">
														<xsl:for-each select="$var26_cur/*[local-name()='timeEnd' and namespace-uri()='']">
															<xsl:variable name="var35_cur" select="."/>
															<date>
																<xsl:attribute name="dateType" namespace="">Collected</xsl:attribute>
																<xsl:value-of select="concat($var27_cur, concat('T', $var30_cur), $var31_cur, '/', $var32_cur, concat('T', .), $var31_cur)"/>
															</date>
														</xsl:for-each>
													</xsl:when>
													<xsl:otherwise>
														<date>
															<xsl:attribute name="dateType" namespace="">Collected</xsl:attribute>
															<xsl:value-of select="concat($var27_cur, concat('T', $var30_cur), $var31_cur, '/', ., '', $var31_cur)"/>
														</date>
													</xsl:otherwise>
												</xsl:choose>
											</xsl:for-each>
										</xsl:for-each>
									</xsl:for-each>
								</xsl:when>
								<xsl:otherwise>
									<xsl:for-each select="$var26_cur/*[local-name()='timezone' and namespace-uri()='']">
										<xsl:variable name="var36_cur" select="."/>
										<xsl:for-each select="$var26_cur/*[local-name()='dateEnd' and namespace-uri()='']">
											<xsl:variable name="var37_cur" select="."/>
											<xsl:variable name="var38_nested">
												<xsl:for-each select="$var2_cur/*[local-name()='SpatialTemporalCoverages' and namespace-uri()='']/*[local-name()='SpatialTemporalCoverage' and namespace-uri()='']">
													<xsl:variable name="var39_cur" select="."/>
													<xsl:value-of select="number(boolean(*[local-name()='timeEnd' and namespace-uri()='']))"/>
												</xsl:for-each>
											</xsl:variable>
											<xsl:choose>
												<xsl:when test="boolean(translate(normalize-space($var38_nested), ' 0', ''))">
													<xsl:for-each select="$var26_cur/*[local-name()='timeEnd' and namespace-uri()='']">
														<xsl:variable name="var40_cur" select="."/>
														<date>
															<xsl:attribute name="dateType" namespace="">Collected</xsl:attribute>
															<xsl:value-of select="concat($var27_cur, '', $var36_cur, '/', $var37_cur, concat('T', .), $var36_cur)"/>
														</date>
													</xsl:for-each>
												</xsl:when>
												<xsl:otherwise>
													<date>
														<xsl:attribute name="dateType" namespace="">Collected</xsl:attribute>
														<xsl:value-of select="concat($var27_cur, '', $var36_cur, '/', ., '', $var36_cur)"/>
													</date>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:for-each>
									</xsl:for-each>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
					</xsl:for-each>
					<date>
						<xsl:attribute name="dateType" namespace="">Created</xsl:attribute>
						<xsl:value-of select="*[local-name()='dateCreated' and namespace-uri()='']"/>
					</date>
				</dates>
				<language>
					<xsl:value-of select="*[local-name()='Language' and namespace-uri()='']/*[local-name()='code' and namespace-uri()='']"/>
				</language>
				<xsl:for-each select="*[local-name()='RelatedWorks' and namespace-uri()='']">
					<xsl:variable name="var41_cur" select="."/>
					<relatedIdentifiers>
						<xsl:for-each select="*[local-name()='RelatedWork' and namespace-uri()='']">
							<xsl:variable name="var42_cur" select="."/>
							<relatedIdentifier>
								<xsl:attribute name="relatedIdentifierType" namespace="">
									<xsl:value-of select="*[local-name()='IdentifierType' and namespace-uri()='']/*[local-name()='name' and namespace-uri()='']"/>
								</xsl:attribute>
								<xsl:attribute name="relationType" namespace="">
									<xsl:value-of select="*[local-name()='Relation' and namespace-uri()='']/*[local-name()='name' and namespace-uri()='']"/>
								</xsl:attribute>
								<xsl:value-of select="*[local-name()='Identifier' and namespace-uri()='']"/>
							</relatedIdentifier>
						</xsl:for-each>
					</relatedIdentifiers>
				</xsl:for-each>
				<xsl:if test="*[local-name()='version' and namespace-uri()='']">
					<xsl:for-each select="*[local-name()='version' and namespace-uri()='']">
						<xsl:variable name="var43_cur" select="."/>
						<version>
							<xsl:value-of select="."/>
						</version>
					</xsl:for-each>
				</xsl:if>
				<rightsList>
					<rights>
						<xsl:attribute name="rightsURI" namespace="">
							<xsl:value-of select="*[local-name()='Rights' and namespace-uri()='']/*[local-name()='rightsURI' and namespace-uri()='']"/>
						</xsl:attribute>
						<xsl:attribute name="rightsIdentifier" namespace="">
							<xsl:value-of select="*[local-name()='Rights' and namespace-uri()='']/*[local-name()='rightsIdentifier' and namespace-uri()='']"/>
						</xsl:attribute>
						<xsl:attribute name="rightsIdentifierScheme" namespace="">SPDX</xsl:attribute>
						<xsl:attribute name="schemeURI" namespace="">https://spdx.org/licenses/</xsl:attribute>
						<xsl:attribute name="xml:lang">en</xsl:attribute>
						<xsl:value-of select="*[local-name()='Rights' and namespace-uri()='']/*[local-name()='text' and namespace-uri()='']"/>
					</rights>
				</rightsList>
				<descriptions>
					<xsl:for-each select="*[local-name()='Descriptions' and namespace-uri()='']/*[local-name()='Description' and namespace-uri()='']">
						<xsl:variable name="var44_cur" select="."/>
						<description>
							<xsl:attribute name="descriptionType" namespace="">
								<xsl:choose>
									<xsl:when test="contains(*[local-name()='type' and namespace-uri()=''], ' ')">
										<xsl:value-of select="concat(substring-before(*[local-name()='type' and namespace-uri()=''], ' '), substring-after(*[local-name()='type' and namespace-uri()=''], ' '))"/>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="*[local-name()='type' and namespace-uri()='']"/>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:attribute>
							<xsl:attribute name="xml:lang">en</xsl:attribute>
							<xsl:value-of select="*[local-name()='description' and namespace-uri()='']"/>
						</description>
					</xsl:for-each>
				</descriptions>
				<geoLocations>
					<xsl:for-each select="*[local-name()='SpatialTemporalCoverages' and namespace-uri()='']/*[local-name()='SpatialTemporalCoverage' and namespace-uri()='']">
						<xsl:variable name="var45_cur" select="."/>
						<geoLocation>
							<xsl:for-each select="*[local-name()='description' and namespace-uri()='']">
								<xsl:variable name="var46_cur" select="."/>
								<geoLocationPlace>
									<xsl:value-of select="."/>
								</geoLocationPlace>
							</xsl:for-each>
							<xsl:if test="not(*[local-name()='latitudeMax' and namespace-uri()=''])">
								<geoLocationPoint>
									<xsl:if test="not((false() and boolean(*[local-name()='longitudeMax' and namespace-uri()=''])))">
										<xsl:for-each select="*[local-name()='longitudeMin' and namespace-uri()='']">
											<xsl:variable name="var47_cur" select="."/>
											<pointLongitude>
												<xsl:value-of select="number(.)"/>
											</pointLongitude>
										</xsl:for-each>
										<xsl:for-each select="*[local-name()='latitudeMin' and namespace-uri()='']">
											<xsl:variable name="var48_cur" select="."/>
											<pointLatitude>
												<xsl:value-of select="number(.)"/>
											</pointLatitude>
										</xsl:for-each>
									</xsl:if>
								</geoLocationPoint>
							</xsl:if>
							<xsl:if test="*[local-name()='latitudeMax' and namespace-uri()='']">
								<geoLocationBox>
									<xsl:if test="(true() and boolean(*[local-name()='longitudeMax' and namespace-uri()='']))">
										<xsl:for-each select="*[local-name()='longitudeMin' and namespace-uri()='']">
											<xsl:variable name="var49_cur" select="."/>
											<westBoundLongitude>
												<xsl:value-of select="number(.)"/>
											</westBoundLongitude>
										</xsl:for-each>
									</xsl:if>
									<xsl:if test="(true() and boolean(*[local-name()='longitudeMax' and namespace-uri()='']))">
										<xsl:for-each select="*[local-name()='longitudeMax' and namespace-uri()='']">
											<xsl:variable name="var50_cur" select="."/>
											<eastBoundLongitude>
												<xsl:value-of select="number(.)"/>
											</eastBoundLongitude>
										</xsl:for-each>
									</xsl:if>
									<xsl:if test="(true() and boolean(*[local-name()='longitudeMax' and namespace-uri()='']))">
										<xsl:for-each select="*[local-name()='latitudeMin' and namespace-uri()='']">
											<xsl:variable name="var51_cur" select="."/>
											<southBoundLatitude>
												<xsl:value-of select="number(.)"/>
											</southBoundLatitude>
										</xsl:for-each>
									</xsl:if>
									<xsl:if test="(true() and boolean(*[local-name()='longitudeMax' and namespace-uri()='']))">
										<xsl:for-each select="*[local-name()='latitudeMax' and namespace-uri()='']">
											<xsl:variable name="var52_cur" select="."/>
											<northBoundLatitude>
												<xsl:value-of select="number(.)"/>
											</northBoundLatitude>
										</xsl:for-each>
									</xsl:if>
								</geoLocationBox>
							</xsl:if>
						</geoLocation>
					</xsl:for-each>
				</geoLocations>
				<xsl:for-each select="*[local-name()='FundingReferences' and namespace-uri()='']">
					<xsl:variable name="var53_cur" select="."/>
					<fundingReferences>
						<xsl:for-each select="*[local-name()='FundingReference' and namespace-uri()='']">
							<xsl:variable name="var54_cur" select="."/>
							<fundingReference>
								<funderName>
									<xsl:value-of select="*[local-name()='funder' and namespace-uri()='']"/>
								</funderName>
								<xsl:if test="*[local-name()='funderid' and namespace-uri()='']">
									<xsl:for-each select="*[local-name()='funderid' and namespace-uri()='']">
										<xsl:variable name="var55_cur" select="."/>
										<funderIdentifier>
											<xsl:attribute name="schemeURI" namespace="">https://www.crossref.org/services/funder-registry/</xsl:attribute>
											<xsl:for-each select="$var54_cur/*[local-name()='funderidtyp' and namespace-uri()='']">
												<xsl:variable name="var56_cur" select="."/>
												<xsl:attribute name="funderIdentifierType" namespace="">
													<xsl:value-of select="."/>
												</xsl:attribute>
											</xsl:for-each>
											<xsl:value-of select="concat('https://doi.org/10.13039/', .)"/>
										</funderIdentifier>
									</xsl:for-each>
								</xsl:if>
								<xsl:if test="((string-length(string(*[local-name()='grantnumber' and namespace-uri()=''])) &gt; 0) and true())">
									<awardNumber>
										<xsl:value-of select="*[local-name()='grantnumber' and namespace-uri()='']"/>
									</awardNumber>
								</xsl:if>
								<xsl:if test="((string-length(string(*[local-name()='grantname' and namespace-uri()=''])) &gt; 0) and true())">
									<awardTitle>
										<xsl:value-of select="*[local-name()='grantname' and namespace-uri()='']"/>
									</awardTitle>
								</xsl:if>
							</fundingReference>
						</xsl:for-each>
					</fundingReferences>
				</xsl:for-each>
			</xsl:for-each>
		</resource>
	</xsl:template>
</xsl:stylesheet>
