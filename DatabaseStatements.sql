/* Tested under SQLite, Brad Howard @ 20:29 on 3/2/2017 */
CREATE DATABASE MagpieDB;

CREATE TABLE Collections 
(
	'CID' INTEGER PRIMARY KEY AUTO_INCREMENT,
	'IsActive' TINYINT(1) DEFAULT 1,
	'Name' VARCHAR(100) NOT NULL,
	'City' VARCHAR(100) DEFAULT "Spokane",
	'State' VARCHAR(100) DEFAULT "Washington",
	'Rating' VARCHAR(100) DEFAULT "E",
	'Description' VARCHAR(1000) NOT NULL, 
	'NumberOfLandMarks' INTEGER DEFAULT 0, 
	'CollectionLength' DOUBLE DEFAULT 0.0,
	'IsOrder' TINYINT(1) DEFAULT 0,
	'PicID' INTEGER DEFAULT 0
);

CREATE TABLE Landmarks
(
	'LID' INTEGER PRIMARY KEY AUTO_INCREMENT,
	'Name' VARCHAR(100) NOT NULL,
	'Longitude' DOUBLE DEFAULT 0.0, 
	'Latitude' DOUBLE DEFAULT 0.0, 
	'NumberOfWalks' INTEGER DEFAULT 0,
	'Description' INTEGER DEFAULT 0,
	'QRCode' VARCHAR(625) DEFAULT "{ EMPTY }",
	'PicID' INTEGER DEFAULT = 0
);

CREATE TABLE CollectionLandmarks 
(
	'CollectionID' INTEGER NOT NULL REFERENCES Collections(CID) ON DELETE CASCADE ON UPDATE CASCADE, 
	'LandMarkID' INTEGER NOT NULL REFERENCES LandMarks(LID) ON DELETE CASCADE ON UPDATE CASCADE, 
	PRIMARY KEY (WalkID, LandMarkID)
);

CREATE TABLE CollectionImages 
(
	'PicID' INTEGER PRIMARY KEY AUTO_INCREMENT,
	'CID' INTEGER DEFAULT 0, 
	'FileLocation' VARCHAR(200) DEFAULT "{ EMPTY }",
	'ImageType' VARCHAR(50) DEFAULT "{ EMPTY }",
	'IsCopyright' TINYINT(1) DEFAULT 0,
	FOREIGN KEY (CID) REFERENCES Collections(CID) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE LandmarkImages 
(
	'PicID' INTEGER PRIMARY KEY AUTO_INCREMENT,
	'LID' INTEGER DEFAULT 0,
	'FileLocation' VARCHAR(200) DEFAULT "{ EMPTY }",
	'ImageType' VARCHAR(50) DEFAULT "{ EMPTY }",
	'IsCopyright' TINYINT(1) DEFAULT 0,
	FOREIGN KEY (LID) REFERENCES LandMarks(LID) ON DELETE CASCADE ON UPDATE CASCADE
);

/* out dated */
CREATE TABLE LandmarkDescription
(
	'DesID' INTEGER PRIMARY KEY AUTO_INCREMENT,
	'LID' INTEGER DEFAULT 0,
	'CID' INTEGER DEFAULT 0,
	'Description' VARCHAR(1000) DEFAULT "{ EMPTY }",
	FOREIGN KEY (LID) REFERENCES LandMarks(LID) ON DELETE CASCADE ON UPDATE CASCADE
	FOREIGN KEY (CID) REFERENCES Collections(CID) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE WebUserData
(
	'UserID' INTEGER PRIMARY KEY,
	'UserName' VARCHAR(50) DEFAULT "{ EMPTY }",
	'UserEmail' VARCHAR(100) DEFAULT "{ EMPTY }",
	'UserData' VARCHAR(5000) DEFAULT "{ EMPTY }"
);

CREATE TABLE AppUserData
(
	'UserID' INTEGER PRIMARY KEY,
	'UserName' VARCHAR(50) DEFAULT "{ EMPTY }",
	'UserEmail' VARCHAR(100) DEFAULT "{ EMPTY }",
	'UserData' VARCHAR(5000) DEFAULT "{ EMPTY }"
);

/* Joins Walks with LandMarks via WalkLandMarks */

SELECT CID, Collections.Name, LID, Landmarks.Name
FROM CollectionLandmarks LEFT JOIN Collections ON CollectionID = CID
JOIN Landmarks ON LandmarkID = LID;

/* Find all info for the Walk and its landmarks */
SELECT DISTINCT Landmarks.LID, Landmarks.Name, Longitude, Latitude, LandmarkDescription.Description, QRCode
FROM LandmarkDescription, CollectionLandmarks, Collections, Landmarks
WHERE Collections.CID = ? AND Landmarks.LID = CollectionLandmarks.LandMarkID AND Collections.CID = CollectionLandmarks.CollectionID AND DesID = DescID
ORDER BY Collections.CID ASC

/* Updates the nunmber of Landmarks for Walks */

UPDATE Collections
SET NumberOfLandMarks = (SELECT COUNT(LandMarkID) FROM CollectionLandmarks WHERE CID = CollectionID);

/* Updates the nunmber of walks for Landmarks */

UPDATE Landmarks
SET NumberOfCollections = (SELECT COUNT(CollectionID) FROM CollectionLandmarks WHERE LID = LandmarkID); 

/* Updates the WalkLength, with place holders for PHP code */

UPDATE Collections
SET CollectionLength = ?
WHERE CID = ?;

/* add walk */

INSERT INTO Collections (Name, City, 'State', Rating, Description, IsOrder, PicID) 
VALUES (?, ?, ?, ?, ?, ?, ?);

/* add landmark */

INSERT INTO Landmarks (Name, Longitude, Latitude, DescID, QRCode, PicID) 
VALUES (?, ?, ?, ?, ?, ?);

/* Deactivate a Collection */

UPDATE Collections
SET IsActive = 0
WHERE CID = ?;

/* (~+.+)~ ~(>.<~) ~(O.o~) ~(@.@;~) ~(-_-~) ~(OwO~) */