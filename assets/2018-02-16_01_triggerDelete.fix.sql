-- Описать LINK
CREATE TABLE "linkDeleted" (
       url                  VARCHAR(255) NOT NULL,
       id_user              INTEGER NOT NULL,
       created              TIMESTAMP NOT NULL,
       id_linkDel              INTEGER PRIMARY KEY AUTOINCREMENT,
       title                VARCHAR(255),
	   FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE
)

CREATE TRIGGER IF NOT EXISTS linkDelete 
   BEFORE DELETE
   ON link
BEGIN
 INSERT INTO linkDeleted(url,id_user,created,id_linkDel,title) 
 VALUES(OLD.url,OLD.id_user,OLD.created,OLD.id_link,OLD.title );
END;