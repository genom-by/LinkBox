CREATE TABLE settings2(
       id_set		INTEGER PRIMARY KEY AUTOINCREMENT,
       name		VARCHAR(64),
       id_user		INTEGER NOT NULL,
       svalue		VARCHAR(64),
       defvalue		VARCHAR(64),
       variants		VARCHAR(255),
       storeCookies	 BIT,
       
	   FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE	   
);

DROP TABLE settings;

ALTER TABLE settings2 RENAME TO settings;

CREATE UNIQUE INDEX XPKsettings ON settings
(
       name
);
