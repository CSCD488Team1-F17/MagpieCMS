/* Tested under SQLite, Brad Howard @ 20:29 on 3/2/2017 */

CREATE TABLE Walks 
(
	WID INTEGER PRIMARY KEY AUTOINCREMENT, /* MySQL -> AUTO_INCREMENT = 1 */
	Name VARCHAR(100) NOT NULL,
	Description VARCHAR(1000) NOT NULL, 
	NumberOfLandMarks INTEGER, 
	WalkLength DOUBLE,
	IsOrder TINYINT(1), /* Boolean */
	WalkPreviewID INTEGER,
	PicID INTEGER,
	FOREIGN KEY (WalkPreviewID) REFERENCES WalkDescription(DesID) ON DELETE CASCADE,
	FOREIGN KEY (PicID) REFERENCES WalkImages(PicID) ON DELETE CASCADE
	/* Optionals pending Client needs */
);

CREATE TABLE LandMarks
(
	LID INTEGER PRIMARY KEY AUTOINCREMENT, /* MySQL -> AUTO_INCREMENT = 1 */
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
	WalkID INTEGER REFERENCES Walks(WID) NOT NULL, 
	LandMarkID INTEGER REFERENCES LandMarks(LID) NOT NULL, 
	PRIMARY KEY (WalkID, LandMarkID)
);

CREATE TABLE WalkImages 
(
	PicID INTEGER PRIMARY KEY AUTOINCREMENT, /* MySQL -> AUTO_INCREMENT = 1 */
	WID INTEGER, 
	FileLocation VARCHAR(200),
	IsCopyright TINYINT(1) DEFAULT 0
);

CREATE TABLE LandMarkImages 
(
	PicID INTEGER PRIMARY KEY AUTOINCREMENT, /* MySQL -> AUTO_INCREMENT = 1 */
	LID INTEGER,
	FileLocation VARCHAR(200),
	IsCopyright TINYINT(1) DEFAULT 0
);

/* out dated */
CREATE TABLE LandMarkDescription
(
	DesID INTEGER PRIMARY KEY AUTOINCREMENT, /* MySQL -> AUTO_INCREMENT = 1 */
	LID INTEGER,
	WID INTEGER,
	Description VARCHAR(1000)
);

CREATE TABLE QRCodes
(
	QRCID INTEGER PRIMARY KEY AUTOINCREMENT, /* MySQL -> AUTO_INCREMENT = 1 */
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