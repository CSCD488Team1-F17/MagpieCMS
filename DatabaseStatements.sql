/* Tested under SQLite, Brad Howard @ 20:29 on 3/2/2017 */
CREATE DATABASE MagpieDB;

CREATE TABLE Walks 
(
	WID INTEGER PRIMARY KEY AUTO_INCREMENT,
	Name VARCHAR(100) NOT NULL,
	Description VARCHAR(1000) NOT NULL, 
	NumberOfLandMarks INTEGER, 
	WalkLength DOUBLE,
	IsOrder TINYINT(1),
	WalkPreviewID INTEGER,
	PicID INTEGER,
	FOREIGN KEY (WalkPreviewID) REFERENCES WalkDescription(DesID) ON DELETE CASCADE,
	FOREIGN KEY (PicID) REFERENCES WalkImages(PicID) ON DELETE CASCADE
);

CREATE TABLE LandMarks
(
	LID INTEGER PRIMARY KEY AUTO_INCREMENT,
	Name VARCHAR(100) NOT NULL,
	Longitude DOUBLE, 
	Latitude DOUBLE, 
	NumberOfWalks INTEGER,
	Description INTEGER,
	QRCodeID INTEGER,
	PicID INTEGER,
	FOREIGN KEY (Description) REFERENCES LandMarkDescription(DesID)	ON DELETE CASCADE,
	FOREIGN KEY (QRCodeID) REFERENCES QRCodes(PicID) ON DELETE CASCADE,
	FOREIGN KEY (PicID) REFERENCES LandMarkImages(PicID) ON DELETE CASCADE
);

CREATE TABLE WalkLandMarks 
(
	WalkID INTEGER NOT NULL REFERENCES Walks(WID), 
	LandMarkID INTEGER NOT NULL REFERENCES LandMarks(LID), 
	PRIMARY KEY (WalkID, LandMarkID)
);

CREATE TABLE WalkImages 
(
	PicID INTEGER PRIMARY KEY AUTO_INCREMENT,
	WID INTEGER, 
	FileLocation VARCHAR(200),
	IsCopyright TINYINT(1) DEFAULT 0
);

CREATE TABLE LandMarkImages 
(
	PicID INTEGER PRIMARY KEY AUTO_INCREMENT,
	LID INTEGER,
	FileLocation VARCHAR(200),
	IsCopyright TINYINT(1) DEFAULT 0
);

/* out dated */
CREATE TABLE LandMarkDescription
(
	DesID INTEGER PRIMARY KEY AUTO_INCREMENT,
	LID INTEGER,
	WID INTEGER,
	Description VARCHAR(1000)
);

CREATE TABLE QRCodes
(
	QRCID INTEGER PRIMARY KEY AUTO_INCREMENT,
	LID INTEGER,
	RawCode VARCHAR(625)
);

/* Joins Walks with LandMarks via WalkLandMarks */

SELECT WID, Walks.Name, LID, LandMarks.Name
FROM WalkLandMarks LEFT JOIN Walks ON WalkID = WID
JOIN LandMarks ON LandMarkID = LID;

/* Updates the nunmber of Landmarks for Walks */

UPDATE walks
SET NumberOfLandMarks = (SELECT COUNT (LandMarkID) FROM WalkLandMarks WHERE WID = WalkID);

/* Updates the nunmber of walks for Landmarks */

UPDATE LandMarks
SET NumberOfWalks = (SELECT COUNT (WalkID) FROM WalkLandMarks WHERE LID = LandMarkID); 

/* Updates the WalkLength, with place holders for PHP code */

UPDATE Walks
SET WalkLength = /*{0}*/
WHERE WID = /*{1}*/;