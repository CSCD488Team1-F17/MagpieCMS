/* Tested under SQLite, Brad Howard @ 20:29 on 3/2/2017 */
CREATE DATABASE MagpieDB;

CREATE TABLE Walks 
(
	WID INTEGER PRIMARY KEY AUTO_INCREMENT,
	Name VARCHAR(100) NOT NULL,
	Description VARCHAR(1000) NOT NULL, 
	NumberOfLandMarks INTEGER DEFAULT 0, 
	WalkLength DOUBLE DEFAULT 0.0,
	IsOrder TINYINT(1) DEFAULT 0,
	WalkPreviewID INTEGER DEFAULT 0,
	PicID INTEGER DEFAULT 0
);

CREATE TABLE LandMarks
(
	LID INTEGER PRIMARY KEY AUTO_INCREMENT,
	Name VARCHAR(100) NOT NULL,
	Longitude DOUBLE DEFAULT 0.0, 
	Latitude DOUBLE DEFAULT 0.0, 
	NumberOfWalks INTEGER DEFAULT 0,
	Description INTEGER DEFAULT 0,
	QRCode VARCHAR(625) DEFAULT "{ EMPTY }",
	PicID INTEGER DEFAULT = 0
);

CREATE TABLE WalkLandMarks 
(
	WalkID INTEGER NOT NULL REFERENCES Walks(WID) ON DELETE CASCADE ON UPDATE CASCADE, 
	LandMarkID INTEGER NOT NULL REFERENCES LandMarks(LID) ON DELETE CASCADE ON UPDATE CASCADE, 
	PRIMARY KEY (WalkID, LandMarkID)
);

CREATE TABLE WalkImages 
(
	PicID INTEGER PRIMARY KEY AUTO_INCREMENT,
	WID INTEGER DEFAULT 0, 
	FileLocation VARCHAR(200) DEFAULT "{ EMPTY }",
	ImageType VARCHAR(50) DEFAULT "{ EMPTY }",
	IsCopyright TINYINT(1) DEFAULT 0,
	FOREIGN KEY (WID) REFERENCES Walks(WID) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE LandMarkImages 
(
	PicID INTEGER PRIMARY KEY AUTO_INCREMENT,
	LID INTEGER DEFAULT 0,
	FileLocation VARCHAR(200) DEFAULT "{ EMPTY }",
	ImageType VARCHAR(50) DEFAULT "{ EMPTY }",
	IsCopyright TINYINT(1) DEFAULT 0,
	FOREIGN KEY (LID) REFERENCES LandMarks(LID) ON DELETE CASCADE ON UPDATE CASCADE
);

/* out dated */
CREATE TABLE LandMarkDescription
(
	DesID INTEGER PRIMARY KEY AUTO_INCREMENT,
	LID INTEGER DEFAULT 0,
	WID INTEGER DEFAULT 0,
	Description VARCHAR(1000) DEFAULT "{ EMPTY }",
	FOREIGN KEY (LID) REFERENCES LandMarks(LID) ON DELETE CASCADE ON UPDATE CASCADE	
);

/* Joins Walks with LandMarks via WalkLandMarks */

SELECT WID, Walks.Name, LID, LandMarks.Name
FROM WalkLandMarks LEFT JOIN Walks ON WalkID = WID
JOIN LandMarks ON LandMarkID = LID;

SELECT Walks.WID, LandMarks.LID, LandMarks.Name, Longitude, Latitude, NumberOfWalks, LandMarkDescription.Description, QRCode, FileLocation, ImageType
FROM WalkLandMarks LEFT JOIN Walks ON WalkID = Walks.WID
JOIN LandMarks ON LandMarkID = LandMarks.LID
JOIN LandMarkImages ON LandMarks.LID = LandMarkImages.LID AND LandMarks.PicID = LandMarkImages.PicID
JOIN LandMarkDescription ON LandMarks.LID = LandMarkDescription.LID AND Walks.WID = LandMarkDescription.WID AND LandMarks.Description = LandMarkDescription.DesID;

SELECT Walks.WID, LandMarks.LID, LandMarks.Name, Longitude, Latitude, LandMarkDescription.Description, QRCode
FROM WalkLandMarks LEFT JOIN Walks ON WalkID = Walks.WID LEFT JOIN LandMarks ON LandMarkID = LandMarks.LID
LEFT JOIN LandMarkDescription ON LandMarks.LID = LandMarkDescription.LID AND LandMarks.Description = LandMarkDescription.DesID
ORDER BY `Walks`.`WID` ASC

/* Updates the nunmber of Landmarks for Walks */

UPDATE walks
SET NumberOfLandMarks = (SELECT COUNT(LandMarkID) FROM WalkLandMarks WHERE WID = WalkID);

/* Updates the nunmber of walks for Landmarks */

UPDATE LandMarks
SET NumberOfWalks = (SELECT COUNT(WalkID) FROM WalkLandMarks WHERE LID = LandMarkID); 

/* Updates the WalkLength, with place holders for PHP code */

UPDATE Walks
SET WalkLength = /*{0}*/
WHERE WID = /*{1}*/;