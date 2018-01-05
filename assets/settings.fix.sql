DROP TABLE settings

CREATE TABLE settings(
  "id_set" INTEGER PRIMARY KEY AUTOINCREMENT,
  "name" VARCHAR(30) NOT NULL UNIQUE,  
   "defvalue" VARCHAR(30) NOT NULL,
   "variants" VARCHAR(90) NOT NULL,
   "storeCookie" BIT DEFAULT true
);

INSERT INTO settings(name,defvalue,variants, storeCookie) 
VALUES('language','en','en|ru|ua',0);
INSERT INTO settings(name,defvalue,variants, storeCookie) 
VALUES('tagsInputStyle','pillbox','pillbox|simple',1);
INSERT INTO settings(name,defvalue,variants, storeCookie) 
VALUES('showFawIcons','y','y|n',0);
INSERT INTO settings(name,defvalue,variants, storeCookie) 
VALUES('fetchSiteTitle','y','y|n',0);

CREATE TABLE user_settings (
       uid               INTEGER NOT NULL,
       setid              INTEGER NOT NULL,
	"value" VARCHAR(30) NOT NULL,      
	   PRIMARY KEY (uid, setid),
	   FOREIGN KEY (uid) REFERENCES user(id_user) ON DELETE CASCADE,	   
	   FOREIGN KEY (setid) REFERENCES settings(id_set) ON DELETE CASCADE	   
);

CREATE UNIQUE INDEX XPKusets ON user_settings
(
       uid,
       setid
);