CREATE TABLE IF NOT EXISTS download_records (email VARCHAR(40), 
                               file_name VARCHAR(255),
                               yt_video_id VARCHAR(15),
                               dt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
