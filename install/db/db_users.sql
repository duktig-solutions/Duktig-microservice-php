DROP USER IF EXISTS 'duktigregular'@'localhost';
DROP USER IF EXISTS 'duktigbackuper'@'localhost';

CREATE USER 'duktigregular'@'localhost' IDENTIFIED BY 'df876g623regular';
CREATE USER 'duktigbackuper'@'localhost' IDENTIFIED BY 'back124dfgfd89g_FDgf';

GRANT INSERT,SELECT,UPDATE,DELETE,EXECUTE ON duktig.* TO 'duktigregular'@'localhost';
GRANT SELECT,EVENT,LOCK TABLES ON *.* TO 'duktigbackuper'@'localhost';

FLUSH PRIVILEGES;