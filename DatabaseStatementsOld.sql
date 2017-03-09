/* no good */

SELECT Walks.WID, LandMarks.LID, LandMarks.Name, Longitude, Latitude, NumberOfWalks, LandMarkDescription.Description, QRCode, FileLocation, ImageType
FROM WalkLandMarks LEFT JOIN Walks ON WalkID = Walks.WID
JOIN LandMarks ON LandMarkID = LandMarks.LID
JOIN LandMarkImages ON LandMarks.LID = LandMarkImages.LID AND LandMarks.PicID = LandMarkImages.PicID
JOIN LandMarkDescription ON LandMarks.LID = LandMarkDescription.LID AND Walks.WID = LandMarkDescription.WID AND LandMarks.Description = LandMarkDescription.DesID;