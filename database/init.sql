
-- This script creates the `detections` table used to log
-- each time a new face is detected by the camera.
--

CREATE TABLE IF NOT EXISTS `detections` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `image_filename` VARCHAR(255) NOT NULL COMMENT 'Filename of the captured image stored on disk',
  `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'The date and time when the detection occurred',
  `camera_id` INT DEFAULT 1 COMMENT 'Identifier for the source camera, for future expansion'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- You can add an index for faster lookups by timestamp
CREATE INDEX idx_timestamp ON detections(timestamp);
